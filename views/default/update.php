<?php
/**
 * @var View $this
 * @var SourceMessage $model
 */

use yii\helpers\Html;
use yii\web\View;
use Zelenin\yii\modules\I18n\models\SourceMessage;
use Zelenin\yii\modules\I18n\Module;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\ActiveForm;

$this->title = Module::t('Update') . ': ' . $model->message;
echo Breadcrumbs::widget(['links' => [
    ['label' => Module::t('Translations'), 'url' => ['index']],
    ['label' => $this->title]
]]);
?>
<div class="message-update">
    <div class="message-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->errorSummary($model) ?>
        <?php foreach ($model->messages as $language => $message) : ?>
            <?= $form->field($model->messages[$language], '[' . $language . ']translation')->label($language) ?>
            <a href="https://translate.google.com/#view=home&op=translate&sl=<?= \Yii::$app->sourceLanguage ?>&tl=<?= $language ?>&text=<?= $model->message ?>" class="btn btn-default btn-sm" role="button" target="_blank">
                <i class="glyphicon glyphicon-globe"></i>
                <?= Module::t('Google Translate to') ?>&nbsp;<?= $language ?>
            </a>
        <?php endforeach; ?>
        <br />
        <br />
        <?= Html::submitButton(Module::t('Update'), ['class' => 'btn btn-success']) ?>
        <?php $form::end(); ?>
    </div>
</div>
