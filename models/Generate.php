<?php

namespace app\models;

use app\components\behaviors\DateTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

class Generate extends \yii\db\ActiveRecord
{

    use DateTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%generate}}';
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
            // TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'procedimiento' => Yii::t('app', 'Procedimiento'),
            'modificacion' => Yii::t('app', 'Modificacion'),
            'consentimiento' => Yii::t('app', 'Consentimiento Actualizado'),
            'alergia' => Yii::t('app', 'Alergia'),
            'productos_comprados' => Yii::t('app', 'PRODUCTOS COMPRADOS'),
            'servicio_adiciona' => Yii::t('app', 'SERVICIONS ADICIONALES'),
            'servicio_agendados' => Yii::t('app', 'SERVICIONS AGENDADOS'),
            'pdflink' => Yii::t('app', 'PDF PATH'),
            'inicio' => Yii::t('app', 'Inicial Pic PATH'),
            'maping' => Yii::t('app', 'Maping Pic PATH'),
            'eyes' => Yii::t('app', 'Eyes Pic PATH'),
            'final' => Yii::t('app', 'Final Pic PATH'),
            'generate_date' => Yii::t('app', 'Created At'),
            'generate_time' => Yii::t('app', 'Created At'),
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
            'procedimiento',
            'modificacion',
            'generate_date',
            'generate_time',
            'consentimiento',
            'alergia',
            'productos_comprados',
            'servicio_adiciona',
            'servicio_agendados',
            'pdflink',
            'inicio',
            'maping',
            'eyes',
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
