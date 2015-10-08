<?php
/**
 * Created for simple-questionnaire
 * in extweb.org with love!
 * Artem Dekhtyar mail@artemd.ru
 * 08.10.2015
 */

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Пригласить соискателя';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="quest-invite">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin() ?>

    <?= $form->field($model, 'quest_id')->dropDownList(\yii\helpers\ArrayHelper::map( \app\models\Quest::find()->where(['status'=>\app\models\Quest::STATUS_ON])->all(), 'id', 'title' ))->label('Анкета'); ?>
    <?= $form->field($model, 'email'); ?>

    <div class="form-group">
        <?= Html::submitButton('Пригласить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>