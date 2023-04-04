<?php

use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Global Settings');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Add-ons'), 'url' => ['/addons']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Twilio SMS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="twilio-global-settings">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="glyphicon glyphicon-cogwheels" style="margin-right: 5px;"></i>
                <?= Yii::t('app', 'Global Settings') ?>
            </h3>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= Html::label(Yii::t('app', 'Account SID'), 'addon_twilio_accountSID', ['class' => 'control-label']) ?>
                        <?= Html::input('text', 'addon_twilio_accountSID', Yii::$app->settings->get('addon_twilio.accountSID'), [
                            'class' => 'form-control',
                        ]) ?>
                        <div class="help-block">
                            <?= Yii::t('app', 'Every message matters.') ?>
                            <?= Html::a(Yii::t('app', 'Get your Twilio API keys'), 'https://www.twilio.com/') ?>.
                        </div>
                    </div>
                </div>
                <div class='col-sm-6'>
                    <div class="form-group">
                        <?= Html::label(Yii::t('app', 'Auth Token'), 'addon_twilio_authToken', ['class' => 'control-label']) ?>
                        <?= Html::input('text', 'addon_twilio_authToken', Yii::$app->settings->get('addon_twilio.authToken'), [
                            'class' => 'form-control',
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='col-sm-6'>
                    <div class="form-group">
                        <?= Html::label(Yii::t('app', 'From'), 'addon_twilio_from', ['class' => 'control-label']) ?>
                        <?= Html::input('text', 'addon_twilio_from', Yii::$app->settings->get('addon_twilio.from'), [
                            'class' => 'form-control',
                        ]) ?>
                        <div class="help-block">
                            <?= Yii::t('app', 'An alphanumeric string giving your sender address. Eg. +15712224361') ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <?= Html::hiddenInput('action', 'global-settings'); ?>
                        <?= Html::submitButton(Html::tag('i', '', [
                                'class' => 'glyphicon glyphicon-ok',
                                'style' => 'margin-right: 2px;',
                            ]) . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>