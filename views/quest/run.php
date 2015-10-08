<?php
/**
 * Created for simple-questionnaire
 * in extweb.org with love!
 * Artem Dekhtyar mail@artemd.ru
 * 08.10.2015
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \yii\web\View $this */
/* @var \app\models\Quest $quest */
/* @var \app\models\Result $model */

$this->title = $quest->title;

?>

<div class="questionnaire-run">
    <h1><?= Html::encode($this->title); ?></h1>


    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'second_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gender')->radioList([$model::GENDER_MALE => 'Мужчина', $model::GENDER_FEMALE => 'Женщина']) ?>

    <?= $form->field($model, 'birthday')->widget(\trntv\yii\datetimepicker\DatetimepickerWidget::className(), [
        'phpDatetimeFormat' => 'yyyy-MM-dd',
    ]) ?>

    <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>

    <?php if (!empty($quest->questions)): ?>
        <?php foreach ($quest->questions as $i => $question): ?>
            <div>
                <p><b><?= $question->text; ?></b></p>
                <?= Html::hiddenInput(Html::getInputName($model, "results[$i][question]"), $question->text); ?>
                <?php
                $type = $question->type;
                if($type == 'radioList')
                    echo $form->field($model, "results[$i][response]")->$type( array_combine($question->question['responses'], $question->question['responses']) )->label(false);
                elseif($type =='fileInput')
                    echo $form->field($model, "results[$i][response]")->$type()->label(false);
                else
                    echo $form->field($model, "results[$i][response]")->$type()->label(false);
                ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


    <div class="form-group text-right">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
