<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.0
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2021 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

namespace app\modules\addons\modules\twilio;

use app\components\rules\RuleEngine;
use app\controllers\AjaxController;
use app\helpers\Liquid;
use app\helpers\SubmissionHelper;
use app\models\Form;
use app\models\FormSubmission;
use app\modules\addons\EventManagerInterface;
use app\modules\addons\FormManagerInterface;
use app\modules\addons\modules\twilio\models\Twilio;
use app\modules\addons\modules\twilio\models\TwilioItem;
use app\modules\addons\modules\twilio\services\TwilioService;
use Exception;
use Yii;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\Response;

class Module extends \yii\base\Module implements EventManagerInterface, FormManagerInterface
{

    public $id = "twilio";
    public $defaultRoute = 'admin/index';
    public $controllerLayout = '@app/views/layouts/main';

    /**
     * @inheritdoc
     */
    public function getDefaultModelClasses()
    {
        return [
            'Twilio' => Twilio::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attachGlobalEvents()
    {
        return [
            'app.form.submission.accepted' => function ($event) {
                $this->onSubmissionAccepted($event);
            },
            'app.form.submission.verified' => function ($event) {
                $this->onSubmissionVerified($event);
            },
            AjaxController::EVENT_FORM_COPIED => function ($event) {
                $this->onFormCopied($event);
            },
        ];
    }

    /**
     * @inheritdoc
     */
    public function attachClassEvents()
    {
        return [
            Form::class => [
                'beforeDelete' => [
                    [Module::class, 'onFormDeleted']
                ]
            ],
            FormSubmission::class => [
                'afterValidate' => [
                    [Module::class, 'onFormSubmissionAfterValidate']
                ],
            ],
            'yii\base\View' => [
                'afterRender' => [
                    ['app\modules\addons\modules\twilio\Module', 'onViewAfterRender']
                ],
            ],
        ];
    }
    /**
     * Event Handler
     * When a Form is Copied
     *
     * @param $event
     */
    public function onFormCopied($event)
    {
        if (isset($event, $event->form, $event->form->id, $event->oldForm, $event->oldForm->id)) {
            $oModels = Twilio::findAll(['form_id' => $event->oldForm->id]);
            foreach ($oModels as $oModel) {
                $model = new Twilio();
                $model->attributes = $oModel->attributes;
                $model->id = null;
                $model->form_id = $event->form->id;
                $model->isNewRecord = true;
                $model->save();

                foreach ($oModel->items as $oItem) {
                    $item = new TwilioItem();
                    $item->attributes = $oItem->attributes;
                    $item->id = null;
                    $item->twilio_id = $model->id;
                    $item->form_id = $event->form->id;
                    $item->isNewRecord = true;
                    $item->save();
                }
            }
        }
    }

    /**
     * Event Handler
     * Before a form model is deleted
     *
     * @param $event
     */
    public static function onFormDeleted($event)
    {
        if (isset($event) && isset($event->sender) && $event->sender instanceof Form && isset($event->sender->id)) {
            $models = Twilio::find()->where(['form_id' => $event->sender->id])->all();
            foreach ($models as $model) {
                $model->delete();
            }
        }
    }

    /**
     * Event Handler
     * After a Form Submission model is validated
     *
     * @param $event
     */
    public static function onFormSubmissionAfterValidate($event)
    {
        if (isset($event, $event->sender, $event->sender->form_id) && $event->sender instanceof FormSubmission) {

            $submissionData = $event->sender->data;
            $models = Twilio::findAll(['form_id' => $event->sender->form_id, 'status' => 1]);

            foreach ($models as $model) {

                foreach ($model->items as $item) {
                    // Skip On Empty
                    if (isset($item->phone_field) && !empty($submissionData[$item->phone_field])) {

                        $value = $submissionData[$item->phone_field];
                        $value = is_array($value) ? implode(',', $value) : $value;
                        $verified = false;

                        try {
                            $session = Yii::$app->session;
                            $verifiedPhoneNumbers = $session->get('verified_phone_numbers', []);
                            if (in_array($value, $verifiedPhoneNumbers)) {
                                $verified = true;
                                // Require new phone number verification with each submit
                                // $verifiedPhoneNumbers = array_diff($verifiedPhoneNumbers, [$value]);
                                // $session->set('verified_phone_numbers', $verifiedPhoneNumbers);
                            }
                        } catch (Exception $e) {
                            Yii::error($e);
                        }

                        if (!$verified) {
                            self::showErrorMessage($item, Yii::t('app', 'Your phone number is not verified.'));
                        }
                    }
                }
            }
        }
    }

    /**
     * Event Handler
     * After a View is rendered
     *
     * @param $event
     * @throws \Exception
     */
    public static function onViewAfterRender($event)
    {
        if (isset($event, $event->sender, $event->sender->context) &&
            isset($event->sender->context->module, $event->sender->context->module->requestedRoute) &&
            $event->sender->context->module->requestedRoute === "app/embed" ) {

            $event->sender->registerJsFile(Yii::getAlias('@web/static_files/js/libs/bootstrap.min.js'), ['depends' => JqueryAsset::class]);
            $formModel = $event->sender->context->getFormModel();
            $models = Twilio::findAll(['form_id' => $formModel->id, 'status' => 1]);
            $itemCode = '';

            foreach ($models as $model) {

                // Urls
                $sendOptUrl = Url::to(['/addons/twilio/check/send-otp', 'id' => $model->id]);
                $verifyOptUrl = Url::to(['/addons/twilio/check/verify-otp', 'id' => $model->id]);

                // Messages
                $titleTxt = Yii::t('app', 'Please enter the verification code');
                $labelTxt = Yii::t('app', 'Verification Code');
                $helpTxt = Yii::t('app', 'A verification code has been sent via SMS. Please enter the code in the field above to verify your phone.');
                $closeTxt = Yii::t('app', 'Close');
                $verifyTxt = Yii::t('app', 'Verify');
                $verifiedTxt = Yii::t('app', 'Thanks, your phone number has been verified.');
                $invalidTxt = Yii::t('app', 'Invalid verification code entered.');
                $errorTxt = Yii::t('app', 'An error occurred while verifying your code. Please try again later.');
                $alreadyVerifiedTxt = Yii::t('app', 'Thanks! Your phone number has already been verified!');
                $systemErrorTxt = Yii::t('app', 'An error occurred sending the verification code. Please try again later.');
                $invalidFormatTxt = Yii::t('app', 'Please enter a valid phone number.');

                foreach ($model->items as $item) {
                    if (!empty($item->phone_field) && !empty($item->button_field)) {
                        // Selectors
                        $phoneFieldSelector = '#' . $item->phone_field;
                        $buttonFieldSelector = '#' . $item->button_field;

                        $itemCode .= <<<CODE

<div class="modal fade" id="modal_{$item->phone_field}" tabindex="-1" role="dialog" aria-labelledby="label_{$item->phone_field}">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="label_{$item->phone_field}" style="font-weight: 500">{$titleTxt}</h3>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="code_{$item->phone_field}" class="control-label">{$labelTxt}:</label>
          <input type="text" class="form-control" id="code_{$item->phone_field}" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code">
          <p class="help-block">{$helpTxt}</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{$closeTxt}</button>
        <button type="button" class="btn btn-primary" id="button_{$item->phone_field}">{$verifyTxt}</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("form").find("{$buttonFieldSelector}").on('click', function(e) {
            var phone_number = $("{$phoneFieldSelector}").val();
            var regEx = /^\+[1-9]\d{10,14}$/;
            
            // Validate E.164 format
            if (regEx.test(phone_number)) {
                // Send
                $.post( "{$sendOptUrl}", { phone_number: phone_number })
                    .done(function(data) {
                        if (typeof data.status !== 'undefined') {
                            if (data.status === 1) {
                                // Elements
                                var modalEl = $('#modal_{$item->phone_field}');
                                var codeEl = $('#code_{$item->phone_field}');
                                var buttonEl = $('#button_{$item->phone_field}');
                                // Show
                                modalEl.on('shown.bs.modal', function() {
                                    codeEl.focus();
                                })
                                modalEl.modal('show');
                                // Verify manually
                                buttonEl.on('click', function (e) {
                                    e.preventDefault();
                                    var code = codeEl.val();
                                    if (code) {
                                        $.post("{$verifyOptUrl}", { phone_number: phone_number, code: code })
                                            .done(function (data) {
                                                // Close
                                                modalEl.modal('hide');                                    
                                                if (typeof data.status !== 'undefined' && data.status === 1) {
                                                    alert('{$verifiedTxt}')
                                                } else {
                                                    alert('{$invalidTxt}');
                                                }
                                            })
                                            .fail(function() {
                                                alert('{$errorTxt}')
                                            });
                                    } else {
                                        alert('{$invalidTxt}');
                                    }
                                });
                            } else if (data.status === 2) {
                                alert('{$alreadyVerifiedTxt}')
                            }
                        } else {
                            alert('{$systemErrorTxt}')
                        }
                    });
            } else {
                alert('{$invalidFormatTxt}')
            }
        });
    });
</script>
CODE;
                    }
                }
            }

            $code = <<<EOT
{$itemCode}
</body>
EOT;

            $content = $event->output;
            $event->output =  str_replace("</body>", $code, $content);
        }
    }

    /**
     * Event Handler
     * When a form submission has been accepted
     *
     * @param $event
     */
    public function onSubmissionAccepted($event)
    {
        /** @var FormSubmission $submissionModel */
        $submissionModel = $event->submission;
        /** @var Form $formModel */
        $formModel = empty($event->form) ? $submissionModel->form : $event->form;
        /** @var array $filePaths */
        $filePaths = empty($event->filePaths) ? [] : $event->filePaths;

        // If file paths are empty, find them by model relation
        if (empty($filePaths)) {
            $fileModels = $submissionModel->files;
            foreach ($fileModels as $fileModel) {
                $filePaths[] = $fileModel->getLink();
            }
        }

        /*******************************
        /* Make API Request
        /*******************************/
        $this->makeRequest($formModel, $submissionModel, $filePaths, FormSubmission::STATUS_ACCEPTED);
    }

    /**
     * Event Handler
     * When a form submission has been verified
     *
     * @param $event
     */
    public function onSubmissionVerified($event)
    {
        /** @var FormSubmission $submissionModel */
        $submissionModel = $event->submission;
        /** @var Form $formModel */
        $formModel = empty($event->form) ? $submissionModel->form : $event->form;
        /** @var array $filePaths */
        $filePaths = empty($event->filePaths) ? [] : $event->filePaths;

        // If file paths are empty, find them by model relation
        if (empty($filePaths)) {
            $fileModels = $submissionModel->files;
            foreach ($fileModels as $fileModel) {
                $filePaths[] = $fileModel->getLink();
            }
        }

        /*******************************
        /* Make API Request
        /*******************************/
        $this->makeRequest($formModel, $submissionModel, $filePaths, FormSubmission::STATUS_VERIFIED);
    }

    /**
     * Make Request to API
     *
     * @param $formModel
     * @param $submissionModel
     * @param array $filePaths
     * @param int $event Event Type
     * @return bool
     */
    public function makeRequest($formModel, $submissionModel, $filePaths, $event)
    {

        $result = false;

        $models = Twilio::findAll(['form_id' => $formModel->id, 'status' => 1]);
        $dataModel = $formModel->formData;
        /** @var array $submissionData */
        $submissionData = $submissionModel->getSubmissionData();
        // Form fields
        $fieldsForEmail = $dataModel->getFieldsForEmail();
        // Submission data in an associative array
        $tokens = SubmissionHelper::prepareDataForReplacementToken($submissionData, $fieldsForEmail);
        // Submission data in a multidimensional array: [0 => ['label' => '', 'value' => '']]
        $fieldData = SubmissionHelper::prepareDataForSubmissionTable($submissionData, $fieldsForEmail);
        // Submission data for rule engine
        $data = SubmissionHelper::prepareDataForRuleEngine($submissionModel->data, $dataModel->getFields());

        /*******************************
        /* Process
        /*******************************/
        foreach ($models as $model) {

            // Only when the required event occurs
            if ($model->event !== $event) {
                continue;
            }

            // By default
            $isValid = true;

            // Conditional Logic
            if (!empty($model->conditions)) {
                $engine = new RuleEngine([
                    'conditions' => $model->conditions,
                    'actions' => [],
                ]);
                $isValid = $engine->matches($data);
            }

            // If the conditions have been met
            if ($isValid) {

                try {

                    /**
                     * Parse text message (with field variables)
                     */

                    // Replace tokens in SMS Message
                    $message = SubmissionHelper::replaceTokens($model->text, $tokens);
                    $message = Liquid::render($message, $tokens);

                    // Replace tokens in SMS To Numbers
                    $to = SubmissionHelper::replaceTokens($model->to, $tokens);
                    $to = Liquid::render($to, $tokens);

                    /**
                     * Parse multiple recipients (Separated by commas)
                     */
                    $recipients = array_filter(explode(',', $to));

                    foreach ($recipients as $recipient) {

                        /**
                         * Send SMS Notification
                         */
                        $apiKey = Yii::$app->settings->get('accountSID', 'addon_twilio', $model->api_key);
                        $apiSecret = Yii::$app->settings->get('authToken', 'addon_twilio', $model->api_secret);
                        $from = Yii::$app->settings->get('from', 'addon_twilio', $model->from);
                        $service = new TwilioService($apiKey, $apiSecret);
                        $result = $service->sendSms(trim($recipient), trim($from), $message);

                    }

                } catch (Exception $e) {

                    // Log exception
                    Yii::error($e);

                }
            }
        }

        return $result;
    }

    /**
     * Show Error Message
     *
     * @param TwilioItem $item
     * @param string $message
     */
    public static function showErrorMessage($item, $message)
    {
        $errors = [
            'field' => $item->phone_field,
            'messages' => [$message],
        ];
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $response->data = array(
            'action'  => 'submit',
            'success' => false,
            'id' => 0,
            'message' => Yii::t('app', 'There is {startTag}an error in your submission{endTag}.', [
                'startTag' => '<strong>',
                'endTag' => '</strong>',
            ]),
            'errors' => [$errors],
        );
        $response->send();
        exit;
    }
}
