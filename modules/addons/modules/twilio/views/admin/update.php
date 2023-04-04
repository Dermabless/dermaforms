<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\addons\modules\twilio\models\Twilio */
/* @var $forms array [id => name] of Form models */
/* @var $itemModel app\modules\addons\modules\twilio\models\TwilioItem */

$this->title = Yii::t('app', 'Update Form');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Add-ons'), 'url' => ['/addons']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Twilio SMS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Form') .' #'.
    $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="twilio-update box box-big box-light">

    <div class="box-header">
        <h3 class="box-title"><?= $this->title ?>
            <span class="box-subtitle">#<?= Html::encode($model->id) ?></span>
        </h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'itemModel' => $itemModel,
        'forms' => $forms,
    ]) ?>

</div>
