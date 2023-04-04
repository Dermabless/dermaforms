<?php

use yii\helpers\Url;
use yii\bootstrap\Html;

$this->title = Yii::t('app', 'Congratulations, you installed Easy Forms');

/* @var $cronUrl string */

?>

<div class="row">
    <div class="col-sm-4">
        <ul class="list-group">
            <li class="list-group-item">
                <?= Yii::t('app', 'Choose language') ?> <?= Html::icon('ok', ['class' => 'text-success']) ?>
            </li>
            <li class="list-group-item">
                <?= Yii::t('app', 'Verify requirements') ?> <?= Html::icon('ok', ['class' => 'text-success']) ?>
            </li>
            <li class="list-group-item">
                <?= Yii::t('app', 'Set up database') ?> <?= Html::icon('ok', ['class' => 'text-success']) ?>
            </li>
            <li class="list-group-item">
                <?= Yii::t('app', 'Install app') ?> <?= Html::icon('ok', ['class' => 'text-success']) ?>
            </li>
            <li class="list-group-item">
                <?= Yii::t('app', 'Create admin account') ?> <?= Html::icon('ok', ['class' => 'text-success']) ?>
            </li>
            <li class="list-group-item list-group-item-current">
                <?= Yii::t('app', 'Finished') ?>
            </li>
        </ul>
    </div>
    <div class="col-sm-8 form-wrapper">
        <?= Html::tag('h4', Yii::t('app', 'Congratulations, you installed Easy Forms'), ['class' => 'step-title']) ?>
        <p class="text-success"><span class="glyphicon glyphicon-ok"> </span>
            <?= Yii::t('app', 'Installation was completed successfully.') ?>
        </p>
        <?= Html::tag('h5', Yii::t('app', 'Next Steps'), ['class' => 'step-title']) ?>
        <ul>
            <li><?= Yii::t('app', 'Add the following cron job to your server') ?>: <br>
                <strong><?= Yii::t('app', 'Frequency') ?>:</strong> Every minute (* * * * *) <br>
                <strong><?= Yii::t('app', 'Command') ?>:</strong> <code>wget -O /dev/null -q -t 1 "<?= $cronUrl ?>"</code>
            </li>
            <?php if (file_exists(Yii::getAlias('@app/easy_forms.sql'))): ?>
            <li><?= Yii::t('app', "Remove the '/install.php' file and '/easy_forms.sql' file from your application.") ?></li>
            <?php else: ?>
            <li><?= Yii::t('app', "Remove the '/install.php' file from your application.") ?></li>
            <?php endif; ?>
        </ul>
        <p class="text-muted">
            <?= Yii::t('app', 'Note: If you have problems in running the cron jobs, get in touch with our support team and we will be more than happy to help you.') ?>
        </p>
        <?= Html::beginForm('', 'post', ['class' => 'form-vertical']) ?>
        <?= Html::submitButton(Yii::t('app', 'Remove Files & Log in'), ['class'=>'btn btn-default']) ?>
        <a href="<?= Url::to(Url::home(true)) ?>" class="btn btn-primary">
            <?= Yii::t('app', 'Log in') ?>
        </a>
        <?= Html::endForm() ?>
    </div>
</div>
