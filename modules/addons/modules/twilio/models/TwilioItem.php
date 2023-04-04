<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.3
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2021 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

namespace app\modules\addons\modules\twilio\models;

use Yii;

/**
 * This is the model class for table "addon_twilio_item".
 *
 * @property int $id
 * @property int|null $twilio_id
 * @property int|null $form_id
 * @property string|null $phone_field
 * @property string|null $button_field
 *
 * @property Twilio $twilio
 */
class TwilioItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_twilio_item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['twilio_id', 'form_id'], 'integer'],
            [['phone_field', 'button_field'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'twilio_id' => Yii::t('app', 'Twilio ID'),
            'form_id' => Yii::t('app', 'Form ID'),
            'phone_field' => Yii::t('app', 'Phone Field'),
            'button_field' => Yii::t('app', 'Button Field'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTwilio()
    {
        return $this->hasOne(Twilio::class,['twilio_id'=>'id']);
    }
}
