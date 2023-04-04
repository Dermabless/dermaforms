<?php

use app\components\widgets\ConditionsBuilder;
use kartik\depdrop\DepDrop;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\addons\modules\twilio\models\Twilio */
/* @var $itemModel app\modules\addons\modules\twilio\models\TwilioItem */
/* @var $form yii\widgets\ActiveForm */
/* @var $forms array [id => name] of Form models */

$fieldsUrl = Url::to(['/addons/twilio/admin/fields']);

?>

<div class="twilio-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row" style="margin-top: 20px;">
        <div class="col-sm-12">
            <?= $form->field($model, 'name')->textInput([
                'placeholder' => Yii::t('app', "Enter a configuration name..."),
                'maxlength' => true,
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'form_id')->widget(Select2::class, [
                'data' => $forms,
                'options' => ['placeholder' => Yii::t('app', 'Select a form...')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(Yii::t('app', 'Form')); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'event')->widget(Select2::class, [
                'data' => \app\helpers\EventHelper::supportedFormEvents(),
            ]); ?>
        </div>
        <div class="col-sm-3">
            <?php if ($model->isNewRecord) { $model->status = 1;} ?>
            <?= $form->field($model, 'status')->widget(SwitchInput::class) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?= ConditionsBuilder::widget([
                'label' => Yii::t('app', 'Conditions'),
            ]) ?>
            <?= $form->field($model, "conditions", ['options' => ['class' => 'hidden']])->hiddenInput() ?>
        </div>
    </div>

    <div class="row">
        <?php if (empty(Yii::$app->settings->get('addon_twilio.accountSID'))): ?>
            <div class="col-sm-6">
                <?= $form->field($model, 'api_key')->textInput(['maxlength' => true]) ?>
            </div>
        <?php endif; ?>
        <?php if (empty(Yii::$app->settings->get('addon_twilio.authToken'))): ?>
            <div class="col-sm-6">
                <?= $form->field($model, 'api_secret')->textInput(['maxlength' => true]) ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($model->isNewRecord): ?>

        <div class="row">
            <div class="col-sm-12">
            <span class="help-block">
                <span class="label label-default"><?= Yii::t('app', 'Note') ?></span>
                <?= Yii::t(
                    'app',
                    'Every message matters.'
                ) ?>
                <?= Html::a(Yii::t('app', 'Get your Twilio API keys'), 'https://www.twilio.com/') ?>.
            </span>
            </div>
        </div>

    <?php endif; ?>

    <div class="row">
        <?php if (empty(Yii::$app->settings->get('addon_twilio.from'))): ?>
            <div class="col-sm-6">
                <?= $form->field($model, 'from')->textInput(['maxlength' => true])
                    ->hint(Yii::t('app', 'An alphanumeric string giving your sender address.')) ?>
            </div>
        <?php endif; ?>
        <div class="col-sm-6">
            <?= $form->field($model, 'to')->textInput(['maxlength' => true])
                ->hint(Yii::t('app', 'A single phone number in international format. To specify multiple recipients, separate each phone number with a comma.')) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'text')->textarea()->hint(Yii::t('app', 'Message sent when a form is successfully submitted.')) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <p class="help-block">
                <span class="label label-default"><?= Yii::t('app', 'Notes') ?></span>
            </p>
            <ul class="help-block">
                <li><?= Yii::t('app', 'Name is used in the OTP Verification message template by Twilio. Eg. Your {Name} verification code is: {code}') ?></li>
                <li><?= Yii::t('app', 'From is the purchased phone number in Twilio.') ?></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <h3><legend><?= Yii::t('app', 'Field Mapping') ?> <small><?= Yii::t('app', 'For OTP Verification') ?></small></legend></h3>
        </div>
    </div>

    <?php if ($model->isNewRecord || count($model->items) === 0): ?>

        <fieldset class="item">
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($itemModel, '[0]phone_field')->widget(DepDrop::class, [
                        'pluginOptions' => [
                            'depends' => ['twilio-form_id'],
                            'placeholder' => Yii::t('app', 'Select...'),
                            'url' => $fieldsUrl,
                        ]
                    ]) ?>
                </div>
                <div class="col-sm-5">
                    <?= $form->field($itemModel, '[0]button_field')->widget(DepDrop::class, [
                        'pluginOptions' => [
                            'depends' => ['twilio-form_id'],
                            'placeholder' => Yii::t('app', 'Select...'),
                            'url' => $fieldsUrl,
                        ]
                    ]) ?>
                </div>
                <div class="col-sm-1">
                    <div class="form-group" style="padding-top: 25px;">
                        <button type="button" class="btn btn-default btn-add">
                            <i class="glyphicon glyphicon-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset id="itemTemplate" class="hide">
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($itemModel, 'phone_field')->widget(DepDrop::class, [
                        'pluginOptions' => [
                            'depends' => ['twilio-form_id'],
                            'placeholder' => Yii::t('app', 'Select...'),
                            'url' => $fieldsUrl,
                            'initialize' => true,
                        ],
                        'pluginEvents' => [
                            "depdrop:afterChange" => "function(event, id, value) { 
                                $(event.currentTarget).attr('disabled', true)
                            }",
                        ],
                    ]) ?>
                </div>
                <div class="col-sm-5">
                    <?= $form->field($itemModel, 'button_field')->widget(DepDrop::class, [
                        'pluginOptions' => [
                            'depends' => ['twilio-form_id'],
                            'placeholder' => Yii::t('app', 'Select...'),
                            'url' => $fieldsUrl,
                            'initialize' => true,
                        ],
                        'pluginEvents' => [
                            "depdrop:afterChange" => "function(event, id, value) { 
                                $(event.currentTarget).attr('disabled', true)
                            }",
                        ],
                    ]) ?>
                </div>
                <div class="col-sm-1">
                    <div class="form-group" style="padding-top: 25px;">
                        <button type="button" class="btn btn-default btn-remove">
                            <i class="glyphicon glyphicon-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </fieldset>

    <?php else: ?>

        <?php foreach($model->items as $i => $item): ?>
            <fieldset class="item">
                <div class="row">
                    <div class="col-sm-6">
                        <?= Html::hiddenInput('item-'.$i.'-phone_field',
                            $item->phone_field, ['id'=>'item-'.$i.'-phone_field']) ?>
                        <?= $form->field($itemModel, '['.$i.']phone_field')->widget(DepDrop::class, [
                            'pluginOptions' => [
                                'depends' => ['twilio-form_id'],
                                'placeholder' => Yii::t('app', 'Select...'),
                                'url' => $fieldsUrl,
                                'params'=>['item-'.$i.'-phone_field'],
                            ]
                        ]) ?>
                    </div>
                    <div class="col-sm-5">
                        <?= Html::hiddenInput('item-'.$i.'-button_field',
                            $item->button_field, ['id'=>'item-'.$i.'-button_field']) ?>
                        <?= $form->field($itemModel, '['.$i.']button_field')->widget(DepDrop::class, [
                            'pluginOptions' => [
                                'depends' => ['twilio-form_id'],
                                'placeholder' => Yii::t('app', 'Select...'),
                                'url' => $fieldsUrl,
                                'params'=>['item-'.$i.'-button_field'],
                            ]
                        ]) ?>
                    </div>
                    <div class="col-sm-1">
                        <button type="button" class="btn btn-default btn-remove" style="margin-top: 25px;">
                            <i class="glyphicon glyphicon-minus"></i>
                        </button>
                    </div>
                </div>
            </fieldset>
        <?php endforeach; ?>

        <fieldset id="itemTemplate" class="hide">
            <div class="row">
                <div class="col-sm-6">
                    <?= Html::hiddenInput('item-phone_field', '', ['id'=>'item-phone_field']) ?>
                    <?= $form->field($itemModel, 'phone_field')->widget(DepDrop::class, [
                        'pluginOptions' => [
                            'depends' => ['twilio-form_id'],
                            'placeholder' => Yii::t('app', 'Select...'),
                            'url' => $fieldsUrl,
                            'params'=>['item-phone_field'],
                            'initialize' => true,
                        ],
                        'pluginEvents' => [
                            "depdrop:afterChange" => "function(event, id, value) { 
                                $(event.currentTarget).attr('disabled', true)
                            }",
                        ],
                    ]) ?>
                </div>
                <div class="col-sm-5">
                    <?= Html::hiddenInput('item-button_field', '', ['id'=>'item-button_field']) ?>
                    <?= $form->field($itemModel, 'button_field')->widget(DepDrop::class, [
                        'pluginOptions' => [
                            'depends' => ['twilio-form_id'],
                            'placeholder' => Yii::t('app', 'Select...'),
                            'url' => $fieldsUrl,
                            'params'=>['item-button_field'],
                            'initialize' => true,
                        ],
                        'pluginEvents' => [
                            "depdrop:afterChange" => "function(event, id, value) { 
                                $(event.currentTarget).attr('disabled', true)
                            }",
                        ],
                    ]) ?>
                </div>
                <div class="col-sm-1">
                    <button type="button" class="btn btn-default btn-remove" style="margin-top: 25px;">
                        <i class="glyphicon glyphicon-minus"></i>
                    </button>
                </div>
            </div>
        </fieldset>

    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

/**
* Conditional Logic
*/

$ruleBuilderURL = Url::to(['/form/rule-builder']);
$initialize = (int)!$model->isNewRecord;

$script = <<<JS

$(document).ready(function(){

    // Load Conditions Widget
    $("body")
        .find('.rule-builder-conditions')
        .attr('id', 'conditions-builder-1')
        .conditionsWidget({
            'field': '#twilio-conditions',
            'url': '{$ruleBuilderURL}',
            'depends': ['twilio-form_id'],
            'initialize': parseInt("{$initialize}")
        })
        .end()

});

JS;

$this->registerJs($script, $this::POS_END, 'twilio-conditions');

$this->registerCss("legend { margin-top: 20px; }");

/**
 * Field Mapping
 */

if ($model->isNewRecord || count($model->items) === 0) {
    $script = <<<JS

$(document).ready(function(){

    var item = 0;

    $('form')
        // Add button click handler
        .on('click', '.btn-add', function() {

            var template = $('#itemTemplate'),
                cloned = template
                    .clone()
                    .removeClass('hide')
                    .addClass('item')
                    .removeAttr('id')
                    .insertBefore(template);
            item++;

            // Update the name attributes
            cloned
                .find('[name="TwilioItem[phone_field]"]')
                    .attr('name', 'TwilioItem[' + item + '][phone_field]')
                    .attr('id', 'twilioitem-' + item + '-phone_field')
                    .removeAttr('disabled')
                    .depdrop({
                        depends: ['twilio-form_id'],
                        url: '{$fieldsUrl}'
                    })
                    .end()
                .find('[name="TwilioItem[button_field]"]')
                    .attr('name', 'TwilioItem[' + item + '][button_field]')
                    .attr('id', 'twilioitem-' + item + '-button_field')
                    .removeAttr('disabled')
                    .depdrop({
                        depends: ['twilio-form_id'],
                        url: '{$fieldsUrl}'
                    })
                    .end()
        })
        // Remove button click handler
        .on('click', '.btn-remove', function() {
            var fieldset = $(this).closest('fieldset');
            fieldset.remove();
        });

})

JS;

} else {

    $script = <<<JS

$(window).on('load', function() {
    $('#twilio-form_id').trigger('depdrop.change');
});

$(document).ready(function(){

    var item = $('.item').length;

    $('.btn-remove').first().removeClass('btn-remove').addClass('btn-add')
        .find('.glyphicon-minus').removeClass('glyphicon-minus').addClass('glyphicon-plus');

    $('form')
        // Add button click handler
        .on('click', '.btn-add', function() {

            var template = $('#itemTemplate'),
                cloned = template
                    .clone()
                    .removeClass('hide')
                    .addClass('item')
                    .removeAttr('id')
                    .insertBefore(template);
            item++;

            // Update the name attributes
            cloned
                .find('[name="TwilioItem[phone_field]"]')
                    .attr('name', 'TwilioItem[' + item + '][phone_field]')
                    .attr('id', 'twilioitem-' + item + '-phone_field')
                    .removeAttr('disabled')
                    .depdrop({
                        depends: ['twilio-form_id'],
                        url: '{$fieldsUrl}'
                    })
                    .end()
                .find('[name="TwilioItem[button_field]"]')
                    .attr('name', 'TwilioItem[' + item + '][button_field]')
                    .attr('id', 'twilioitem-' + item + '-button_field')
                    .removeAttr('disabled')
                    .depdrop({
                        depends: ['twilio-form_id'],
                        url: '{$fieldsUrl}'
                    })
                    .end()
        })
        // Remove button click handler
        .on('click', '.btn-remove', function() {
            var fieldset = $(this).closest('fieldset');
            fieldset.remove();
        });

})

JS;
}

$this->registerJs($script, $this::POS_END);

