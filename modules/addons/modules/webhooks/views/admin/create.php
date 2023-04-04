<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\addons\modules\webhooks\models\Webhook */
/* @var $forms array [id => name] of Form models */

$this->title = Yii::t('app', 'Create Webhook');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Add-ons'), 'url' => ['/addons']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Webhooks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="webhooks-create box box-big box-light">

    <div class="box-header">
        <h3 class="box-title"><?= Yii::t('app', 'Create Webhook') ?>
            <span class="box-subtitle"><?= Html::encode($model->id) ?></span>
        </h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'forms' => $forms,
    ]) ?>

</div>
