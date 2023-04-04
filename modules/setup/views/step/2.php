<?php

use yii\helpers\Url;
use yii\bootstrap\Html;
use app\modules\setup\helpers\Requirements;
use app\modules\setup\helpers\RequirementChecker;

$this->title = Yii::t('app', 'Verify requirements');

$passed = true;
$frameworkPath = Yii::getAlias('@app') . '/vendor/yiisoft/yii2';
$requirementChecker = new RequirementChecker();
?>

<div class="row">
    <div class="col-sm-4">
        <ul class="list-group">
            <li class="list-group-item">
                <?= Yii::t('app', 'Choose language') ?>  <?= Html::icon('ok', ['class' => 'text-success'])  ?></li>
            <li class="list-group-item list-group-item-current"><?= Yii::t('app', 'Verify requirements') ?></li>
            <li class="list-group-item"><?= Yii::t('app', 'Set up database') ?></li>
            <li class="list-group-item"><?= Yii::t('app', 'Install app') ?></li>
            <li class="list-group-item"><?= Yii::t('app', 'Create admin account') ?></li>
            <li class="list-group-item"><?= Yii::t('app', 'Finished') ?></li>
        </ul>
    </div>
    <div class="col-sm-8 form-wrapper">
        <?= Html::tag('h4', Yii::t('app', 'Verify requirements'), ['class' => 'step-title']) ?>
        <?php $requirementChecker->check(Requirements::all())->render(); ?>

        <?php if ($requirementChecker->getResult()) : ?>
            <a href="<?= Url::to(['3']) ?>" class="btn btn-primary">
                <?= Yii::t('app', 'Continue') ?>
            </a>
        <?php endif; ?>
    </div>
</div>