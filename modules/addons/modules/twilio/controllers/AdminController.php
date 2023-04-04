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

namespace app\modules\addons\modules\twilio\controllers;

use app\components\User;
use app\helpers\ArrayHelper;
use app\models\Form;
use app\modules\addons\modules\twilio\models\Twilio;
use app\modules\addons\modules\twilio\models\TwilioItem;
use app\modules\addons\modules\twilio\models\TwilioSearch;
use Exception;
use kartik\depdrop\DepDropAction;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AdminController implements the CRUD actions for Sms model.
 */
class AdminController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['actions' => ['index', 'fields'], 'allow' => true, 'roles' => ['configureFormsWithAddons'], 'roleParams' => function() {
                        return ['listing' => true];
                    }],
                    ['actions' => ['create'], 'allow' => true, 'matchCallback' => function ($rule, $action) {
                        if (Yii::$app->request->isGet && Yii::$app->user->can('configureFormsWithAddons', ['listing' => true])) {
                            return true;
                        } else if ($postData = Yii::$app->request->post('Twilio')) {
                            if (isset($postData['form_id'])) {
                                if (is_array($postData['form_id'])) {
                                    return ['modelClass' => Form::class, 'ids' => $postData['form_id']];
                                } else {
                                    return ['model' => Form::findOne(['id' => $postData['form_id']])];
                                }
                            } else {
                                return true; // Allow access. This request is not saving data.
                            }
                        }
                        return false;
                    }],
                    ['actions' => ['view', 'update', 'delete'], 'allow' => true, 'roles' => ['configureFormsWithAddons'], 'roleParams' => function() {
                        $model = $this->findModel(Yii::$app->request->get('id'));
                        return ['model' => $model->form];
                    }],
                    ['actions' => ['update-status', 'delete-multiple'], 'allow' => true, 'roles' => ['configureFormsWithAddons'], 'roleParams' => function() {
                        $models = Twilio::find()
                            ->where(['in', 'id', Yii::$app->request->post('ids')])
                            ->asArray()->all();
                        $ids = ArrayHelper::getColumn($models, 'form_id');
                        return ['modelClass' => Form::class, 'ids' => $ids];
                    }],
                    ['actions' => ['settings'], 'allow' => true, 'roles' => ['configureAddons'], 'roleParams' => function() {
                        return ['listing' => true];
                    }],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'delete-multiple' => [
                'class' => 'app\components\actions\DeleteMultipleAction',
                'modelClass' => 'app\modules\addons\modules\twilio\models\Twilio',
                'afterDeleteCallback' => function () {
                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('app', 'The selected items have been successfully deleted.')
                    );
                },
            ],
            'fields' => [
                'class' => DepDropAction::class,
                'outputCallback' => function ($formID, $params) {
                    $output = array();
                    $form = Form::findOne(['id' => $formID]);
                    if ($form) {
                        if (Yii::$app->user->can('configureFormsWithAddons', ['model' => $form])) {
                            $formDataModel = $form->formData;
                            if ($formDataModel) {
                                $fields = $formDataModel->getFieldsForEmail();
                                foreach ($fields as $name => $label) {
                                    array_push($output, [
                                        'id' => $name,
                                        'name' => $label,
                                    ]);
                                }
                            }
                        }
                    }
                    return $output;
                },
                'selectedCallback' => function ($formID, $params) {
                    if (isset($params[0]) && !empty($params[0])) {
                        return $params[0];
                    }
                }
            ],
        ];
    }

    /**
     * Lists all Sms models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TwilioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sms model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;

        $model = new Twilio();
        $itemModel = new TwilioItem();

        try {
            if ($request->isPost && $model->load($request->post())) {

                $items = Yii::$app->request->post('TwilioItem',[]);

                if (!empty($items)) {
                    if ($model->validate()) {
                        $model->items = $items;
                        $model->save(false);
                        Yii::$app->getSession()->setFlash(
                            'success',
                            Yii::t('app', 'The form has been successfully configured.')
                        );
                        return $this->redirect(['index']);
                    } else {
                        // Show error message
                        Yii::$app->getSession()->setFlash(
                            'danger',
                            Yii::t('app', 'Invalid settings found. Please verify you configuration.')
                        );
                    }
                }
            }

        } catch (Exception $e) {
            // Log
            Yii::error($e);
            // Show error message
            Yii::$app->getSession()->setFlash('danger', $e->getMessage());
        }

        /** @var User $currentUser */
        $currentUser = Yii::$app->user;
        $forms = $currentUser->forms()->orderBy('updated_at DESC')->asArray()->all();
        $forms = ArrayHelper::map($forms, 'id', 'name');

        return $this->render('create', [
            'model' => $model,
            'itemModel' => $itemModel,
            'forms' => $forms,
        ]);
    }

    /**
     * Updates an existing Sms model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->items = Yii::$app->request->post('TwilioItem',[]);
            $model->save();
            Yii::$app->getSession()->setFlash(
                'success',
                Yii::t('app', 'The form has been successfully updated.')
            );
            return $this->redirect(['index']);
        }

        /** @var User $currentUser */
        $currentUser = Yii::$app->user;
        $forms = $currentUser->forms()->orderBy('updated_at DESC')->asArray()->all();
        $forms = ArrayHelper::map($forms, 'id', 'name');

        return $this->render('update', [
            'model' => $model,
            'itemModel' => new TwilioItem,
            'forms' => $forms,
        ]);
    }

    /**
     * Enable / Disable multiple Smss
     *
     * @param $status
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionUpdateStatus($status)
    {
        $accounts = Twilio::findAll(['id' => Yii::$app->getRequest()->post('ids')]);

        if (empty($accounts)) {
            throw new NotFoundHttpException(Yii::t('app', 'Page not found.'));
        }

        foreach ($accounts as $account) {
            $account->status = $status;
            $account->update();
        }

        Yii::$app->getSession()->setFlash(
            'success',
            Yii::t('app', 'The selected items have been successfully updated.')
        );

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Sms model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'The sms configuration has been successfully deleted.'));

        return $this->redirect(['index']);
    }

    /**
     * Global settings for Subscriptions
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSettings()
    {
        $request = Yii::$app->request;
        $settings = Yii::$app->settings;

        if ($request->post()) {
            if ($request->post('action') === 'global-settings') {
                $settings->set('addon_twilio.accountSID', strip_tags($request->post('addon_twilio_accountSID', $settings->get('addon_twilio.accountSID'))));
                $settings->set('addon_twilio.authToken', strip_tags($request->post('addon_twilio_authToken', $settings->get('addon_twilio.authToken'))));
                $settings->set('addon_twilio.from', strip_tags($request->post('addon_twilio_from', $settings->get('addon_twilio.from'))));

                // Show success alert
                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('app', 'Global Settings have been successfully updated.')
                );
            }
        }

        return $this->render('settings');
    }

    /**
     * Finds the Sms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Twilio the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Twilio::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}