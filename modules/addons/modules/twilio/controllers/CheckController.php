<?php


namespace app\modules\addons\modules\twilio\controllers;


use app\modules\addons\modules\twilio\models\Twilio;
use app\modules\addons\modules\twilio\services\TwilioService;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CheckController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'send-otp' => ['post'],
                    'verify-otp' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Send OTP
     *
     * @param $id
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionSendOtp($id)
    {
        $model = $this->findModel($id);
        $phoneNumber = Yii::$app->request->post('phone_number');
        $status = 0;
        $session = Yii::$app->session;
        $verifiedPhoneNumbers = $session->get('verified_phone_numbers', []);

        if (in_array($phoneNumber, $verifiedPhoneNumbers)) {
            $status = 2;
        } elseif (!empty($phoneNumber)) {
            $apiKey = Yii::$app->settings->get('accountSID', 'addon_twilio', $model->api_key);
            $apiSecret = Yii::$app->settings->get('authToken', 'addon_twilio', $model->api_secret);
            $service = new TwilioService($apiKey, $apiSecret);
            $serviceID = $model->getVerificationServiceID();
            if (!empty($serviceID)) {
                $service->sendVerificationToken($serviceID, $phoneNumber);
                $status = 1;
            }
        }

        /** @var Response $response */
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $response->data = [
            'status' => $status,
        ];
        $response->send();
        exit;
    }

    /**
     * Verify OTP
     *
     * @param $id
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionVerifyOtp($id)
    {
        $model = $this->findModel($id);
        $phoneNumber = Yii::$app->request->post('phone_number');
        $code = Yii::$app->request->post('code');
        $status = 0;
        $session = Yii::$app->session;
        $verifiedPhoneNumbers = $session->get('verified_phone_numbers', []);

        if (in_array($phoneNumber, $verifiedPhoneNumbers)) {
            $status = 2;
        } elseif (!empty($code)) {
            $apiKey = Yii::$app->settings->get('accountSID', 'addon_twilio', $model->api_key);
            $apiSecret = Yii::$app->settings->get('authToken', 'addon_twilio', $model->api_secret);
            $service = new TwilioService($apiKey, $apiSecret);
            $serviceID = $model->getVerificationServiceID();
            if (!empty($serviceID)) {
                $response = $service->checkVerificationToken($serviceID, $phoneNumber, $code);
                if (isset($response['status']) && $response['status'] === 'approved') {
                    $status = 1;
                    $session = Yii::$app->session;
                    $verifiedPhoneNumbers = $session->get('verified_phone_numbers', []);
                    if (!in_array($phoneNumber, $verifiedPhoneNumbers)) {
                        $verifiedPhoneNumbers[] = $phoneNumber;
                        $session->set('verified_phone_numbers', $verifiedPhoneNumbers);
                    }
                }
            }
        }

        /** @var Response $response */
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $response->data = [
            'status' => $status,
        ];
        $response->send();
        exit;
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Twilio ID
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