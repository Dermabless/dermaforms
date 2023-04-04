<?php

namespace app\models;

use app\components\behaviors\DateTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

class Micelania extends \yii\db\ActiveRecord
{

    use DateTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%micelania}}';
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'generate_date' => Yii::t('app', 'Created At'),
            'generate_time' => Yii::t('app', 'Created At'),
            'send_time' => Yii::t('app', 'Send At'),
            'procedimiento' => Yii::t('app', 'Procedimiento'),
            'modificacion' => Yii::t('app', 'Modificacion'),
            'especialista' => Yii::t('app', 'Especialista'),
            'consentimiento' => Yii::t('app', 'Consentimiento Actualizado'),
            'alergia' => Yii::t('app', 'Alergia'),
            'tamno' => Yii::t('app', 'Tamno'),
            'anotaciones_generales' => Yii::t('app', 'PRODUCTOS GENERALES'),
            'productos_mtto' => Yii::t('app', 'PRODUCTOS MTTO'),
            'product_adicion' => Yii::t('app', 'PRODUCTOS ADICIONALES'),
            'servicio_furtros' => Yii::t('app', 'SERVICIONS FUTROS'),
            'pdflink' => Yii::t('app', 'PDF PATH'),
            'inicio' => Yii::t('app', 'Inicial Pic PATH'),
            'final' => Yii::t('app', 'Final Pic PATH'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'generate_date',
            'generate_time',
            'send_time',
            'procedimiento',
            'modificacion',
            'especialista',
            'consentimiento',
            'alergia',
            'tamno',
            'anotaciones_generales',
            'productos_mtto',
            'product_adicion',
            'servicio_furtros',
            'pdflink',
            'inicio',
            'final',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (empty($this->generate_date)) {
            $this->generate_date = date("Y-m-d");
        }

        if (empty($this->generate_time)) {
            $this->generate_time = date("H:i:s");
        }

        if (parent::beforeSave($insert)) {
            return true;
        } else {
            return false;
        }
    }
}
