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

namespace app\modules\addons\modules\twilio\models;

use app\components\behaviors\DateTrait;
use app\components\behaviors\RelationTrait;
use app\models\Form;
use app\models\User;
use app\modules\addons\modules\twilio\services\TwilioService;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%addon_twilio}}".
 *
 * @property integer $id
 * @property integer $form_id
 * @property string $name
 * @property string $conditions
 * @property integer $event
 * @property string $platform
 * @property string $api_key
 * @property string $api_secret
 * @property string $to
 * @property string $from
 * @property integer $status
 * @property string $text
 * @property string $verification_service
 *
 * @property Form $form
 * @property User $author
 * @property User $lastEditor
 * @property TwilioItem[] $items
 */
class Twilio extends \yii\db\ActiveRecord
{
    use RelationTrait, DateTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%addon_twilio}}';
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'form_id', 'to', 'text'], 'required'],
            [['api_key'], 'required', 'when' => function ($model) {
                return empty(trim(Yii::$app->settings->get('addon_twilio.accountSID')));
            }, 'whenClient' => "function (attribute, value) {
                return $(\"#twilio-api_key\").length;
            }"],
            [['api_secret'], 'required', 'when' => function ($model) {
                return empty(trim(Yii::$app->settings->get('addon_twilio.authToken')));
            }, 'whenClient' => "function (attribute, value) {
                return $(\"#twilio-api_secret\").length;
            }"],
            [['from'], 'required', 'when' => function ($model) {
                return empty(trim(Yii::$app->settings->get('addon_twilio.from')));
            }, 'whenClient' => "function (attribute, value) {
                return $(\"#twilio-from\").length;
            }"],
            [['event', 'form_id', 'status'], 'integer'],
            [['conditions', 'text'], 'string'],
            [['name', 'platform', 'api_key', 'api_secret', 'to', 'from'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'form_id' => Yii::t('app', 'Form ID'),
            'name' => Yii::t('app', 'Name'),
            'conditions' => Yii::t('app', 'Conditions'),
            'event' => Yii::t('app', 'Event'),
            'platform' => Yii::t('app', 'Platform'),
            'api_key' => Yii::t('app', 'Account SID'),
            'api_secret' => Yii::t('app', 'Auth Token'),
            'to' => Yii::t('app', 'To'),
            'from' => Yii::t('app', 'From'),
            'status' => Yii::t('app', 'Status'),
            'text' => Yii::t('app', 'Message Body'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForm()
    {
        return $this->hasOne(Form::class, ['id' => 'form_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastEditor()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(TwilioItem::class, ['twilio_id' => 'id']);
    }

    public function setItems($value)
    {
        $this->loadRelated('items', $value);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            TwilioItem::deleteAll(["twilio_id" => $this->id]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $apiKey = Yii::$app->settings->get('addon_twilio', 'accountSID', $this->api_key);
        $apiSecret = Yii::$app->settings->get('addon_twilio', 'authToken', $this->api_secret);
        $service = new TwilioService($apiKey, $apiSecret);
        $verificationService = $service->createVerificationService($this->name);
        $this->verification_service = is_string($verificationService) ? $verificationService : json_encode($verificationService, true);

        return true;
    }

    public function getVerificationServiceID()
    {
        $verificationID = null;

        if (!empty($this->verification_service)) {
            $verificationService = json_decode($this->verification_service, true);
            $verificationID = !empty($verificationService['sid']) ? $verificationService['sid'] : null;
        }

        return $verificationID;
    }
}
