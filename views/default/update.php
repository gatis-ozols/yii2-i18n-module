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
            <div class="four wide column">
                <?= $form->field($model->messages[$language], '[' . $language . ']translation')->label($language) ?>
            </div>
        <?php endforeach; ?>
        <?= Html::submitButton(Module::t('Update'), ['class' => 'btn btn-success']) ?>
        <?php $form::end(); ?>
    </div>
</div>
