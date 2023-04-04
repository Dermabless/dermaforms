<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\addons\modules\webhooks\models\Webhook */

$this->title = Yii::t('app', 'Webhook') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Add-ons'), 'url' => ['/addons']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Webhooks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="webhooks-view box box-big box-light">


    <div class="pull-right" style="margin-top: -5px">
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ', ['update', 'id' => $model->id], [
            'title' => Yii::t('app', 'Update'),
            'class' => 'btn btn-sm btn-info']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-bin"></span> ', ['delete', 'id' => $model->id], [
            'title' => Yii::t('app', 'Delete'),
            'class' => 'btn btn-sm btn-danger',
            'data' => [
                'confirm' => Yii::t(
                    'app',
                    'Are you sure you want to delete this webhook? All data related to this item will be deleted. This action cannot be undone.'
                ),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <div class="box-header">
        <h3 class="box-title"><?= Yii::t('app', 'Webhook') ?>
            <span class="box-subtitle"><?= $model->id ?></span>
        </h3>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'form',
                'format'=>'raw',
                'value'=> Html::encode($model->form->name),
            ],
            'url',
            'handshake_key',
            [
                'attribute'=>'status',
                'format'=>'raw',
                'value'=> ($model->status === 1) ? '<span class="label label-success"> '.
                    Yii::t('app', 'ON').' </span>' : '<span class="label label-danger"> '.
                    Yii::t('app', 'OFF').' </span>',
                'type'=>DetailView::INPUT_SWITCH,
                'widgetOptions' => [
                    'pluginOptions' => [
                        'onText' => Yii::t('app', 'ON'),
                        'offText' => Yii::t('app', 'OFF'),
                    ]
                ],
            ],
            [
                'attribute'=>'json',
                'format'=>'raw',
                'value'=> ($model->json === 1) ? '<span class="label label-success"> '.
                    Yii::t('app', 'ON').' </span>' : '<span class="label label-danger"> '.
                    Yii::t('app', 'OFF').' </span>',
                'type'=>DetailView::INPUT_SWITCH,
                'widgetOptions' => [
                    'pluginOptions' => [
                        'onText' => Yii::t('app', 'ON'),
                        'offText' => Yii::t('app', 'OFF'),
                    ]
                ],
            ],
            [
                'attribute'=>'alias',
                'format'=>'raw',
                'value'=> ($model->alias === 1) ? '<span class="label label-success"> '.
                    Yii::t('app', 'ON').' </span>' : '<span class="label label-danger"> '.
                    Yii::t('app', 'OFF').' </span>',
                'type'=>DetailView::INPUT_SWITCH,
                'widgetOptions' => [
                    'pluginOptions' => [
                        'onText' => Yii::t('app', 'ON'),
                        'offText' => Yii::t('app', 'OFF'),
                    ]
                ],
            ],
        ],
    ]) ?>
</div>