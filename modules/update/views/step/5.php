<?php

use yii\helpers\Url;
use yii\bootstrap\Html;

$this->title = Yii::t('app', 'Congratulations, you have updated Easy Forms!');

?>

<div class="row">
    <div class="col-sm-4">
        <ul class="list-group">
            <li class="list-group-item">
                <?= Yii::t('app', 'Choose language') ?> <?= Html::icon('ok', ['class' => 'text-success']) ?>
            </li>
            <li class="list-group-item">
                <?= Yii::t('app', 'Requirements') ?> <?= Html::icon('ok', ['class' => 'text-success']) ?>
            </li>
            <li class="list-group-item">
                <?= Yii::t('app', 'Update app') ?> <?= Html::icon('ok', ['class' => 'text-success']) ?>
            </li>
            <li class="list-group-item list-group-item-current">
                <?= Yii::t('app', 'Finished') ?> <?= Html::icon('ok', ['class' => 'text-success']) ?>
            </li>
        </ul>
    </div>
    <div class="col-sm-8 form-wrapper">
        <?= Html::tag('h4', Yii::t('app', 'Congratulations, you have updated Easy Forms!'), ['class' => 'step-title']) ?>
        <p class="text-success"><span class="glyphicon glyphicon-ok"> </span>
            <?= Yii::t('app', 'You have successfully updated your Easy Forms application.') ?>
        </p>
        <?= Html::tag('h5', Yii::t('app', 'Next Step'), ['class' => 'step-title']) ?>
        <ul>
            <li>
                <?= Yii::t(
                    'app',
                    'For security reasons, you must comment again the uncommented line in the "config/web.php" file.'
                ) ?>
            </li>
        </ul>
        <p class="text-muted">
            <?= Yii::t('app', 'Note: If you have problems running your site, get in touch with our support team and we will be more than happy to help you.') ?>
        </p>
        <a href="<?= Url::to(Url::home(true)) ?>" class="btn btn-primary">
            <?= Yii::t('app', 'Go to Easy Forms') ?>
        </a>
    </div>
</div>
