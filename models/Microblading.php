<?php

namespace app\models;

use app\components\behaviors\DateTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

class Microblading extends \yii\db\ActiveRecord
{

    use DateTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%microblading}}';
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
            'tiempo' => Yii::t('app', 'Timepo'),
            'consentimiento' => Yii::t('app', 'Consentimiento Actualizado'),
            'condiciones_previas' => Yii::t('app', 'Condiciones Previas'),
            'color_anterior' => Yii::t('app', 'Color Anterior'),
            'pigmentos' => Yii::t('app', 'Pigmentos'),
            'proporcion' => Yii::t('app', 'Proporcion'),
            'sangrado' => Yii::t('app', 'Sangrado'),
            'shading' => Yii::t('app', 'Shading'),
            'mascar' => Yii::t('app', 'Mascar'),
            'anotaciones_generales' => Yii::t('app', 'PRODUCTOS GENERALES'),
            'productos_mtto' => Yii::t('app', 'PRODUCTOS MTTO'),
            'product_adicion' => Yii::t('app', 'PRODUCTOS ADICIONALES'),
            'servicio_furtros' => Yii::t('app', 'SERVICIONS FUTROS'),
            'pdflink' => Yii::t('app', 'PDF PATH'),
            'inicial_1' => Yii::t('app', 'Inicial Pic1 PATH'),
            'inicial_2' => Yii::t('app', 'Inicial Pic2 PATH'),
            'inicial_3' => Yii::t('app', 'Inicial Pic3 PATH'),
            'inicial_4' => Yii::t('app', 'Inicial Pic4 PATH'),
            'design_1' => Yii::t('app', 'Design Pic1 PATH'),
            'design_2' => Yii::t('app', 'Design Pic2 PATH'),
            'final_1' => Yii::t('app', 'Final Pic1 PATH'),
            'final_2' => Yii::t('app', 'Final Pic2 PATH'),
            'final_3' => Yii::t('app', 'Final Pic3 PATH'),
            'final_4' => Yii::t('app', 'Final Pic4 PATH'),
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
            'tiempo',
            'consentimiento',
            'condiciones_previas',
            'color_anterior',
            'pigmentos',
            'proporcion',
            'sangrado',
            'shading',
            'mascar',
            'anotaciones_generales',
            'productos_mtto',
            'product_adicion',
            'servicio_furtros',
            'pdflink',
            'inicial_1',
            'inicial_2',
            'inicial_3',
            'inicial_4',
            'design_1',
            'design_2',
            'final_1',
            'final_2',
            'final_3',
            'final_4',
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
