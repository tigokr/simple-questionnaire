<?php
/**
 * Created for simple-questionnaire
 * in extweb.org with love!
 * Artem Dekhtyar mail@artemd.ru
 * 07.10.2015
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/** @var \app\models\Question[] $questions */
/** @var \app\models\Quest $quest */
/** @var \yii\web\View $this */

$this->title = $quest->title . ' — Вопросы';
$this->params['breadcrumbs'][] = ['label' => 'Анкеты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $quest->title, 'url' => ['view', 'id' => $quest->id]];
$this->params['breadcrumbs'][] = 'Вопросы';

$remove_question = <<< JS
    $('.remove-question').bind('click', function(){
        if(confirm('Вы уверены, что хотите это сделать?'))
            $(this).closest('.item').remove();
    });

    $('.questions-update').on('click', '.remove-answer', function(){
        if(confirm('Вы уверены, что хотите это сделать?'))
            $(this).closest('.row').remove();
    });
    $('.questions-update').on('click', '.add-answer', function(){
           var parent = $(this).closest('.row').parent();
           $(this)
            .closest('.row')
                .clone()
                .appendTo(parent);
    })

    $('.question-type').bind('change', function(){
        if($(this).val() != 'radioList' && $(this).hasClass('radioList')) {
            if(confirm('Вы потеряте введённые данные. Продолжить?')) {
                $(this)
                .removeClass('radioList')
                    .closest('.row')
                        .find('.question')
                        .removeClass('col-sm-4')
                        .addClass('col-sm-8')
                .end()
                .closest('.row')
                    .find('.answers')
                    .addClass('hidden')
                ;
            } else {
                $(this).val('radioList');
            }
        }

        if($(this).val() == 'radioList') {
            $(this)
            .addClass('radioList')
                .closest('.row')
                    .find('.question')
                    .removeClass('col-sm-8')
                    .addClass('col-sm-4')
            .end()
                .closest('.row')
                    .find('.answers')
                    .removeClass('hidden')
            ;
        }
    });
JS;

$this->registerJs($remove_question);

?>

<div class="questions-update">
    <?php $form = \yii\bootstrap\ActiveForm::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="container">
        <?php
        foreach ($questions as $i => $model):
            $isRadio = Html::getAttributeValue($model, "[$i]type") == 'radioList';
            ?>
            <div class="item">
                <h3>Вопрос №<?= $i + 1; ?></h3>

                <div class="row">
                    <div class="col-sm-3">
                        <?= Html::hiddenInput(Html::getInputName($model, "[$i]id"), Html::getAttributeValue($model, "[$i]id")); ?>
                        <?= $form->field($model, "[$i]type")->dropDownList(\app\models\Question::type(), ['class' => 'form-control question-type ' . Html::getAttributeValue($model, "[$i]type")]) ?>
                    </div>
                    <div class="col-sm-<?= $isRadio ? '4' : '8'; ?> question">
                        <?= $form->field($model, "[$i]text")->textarea() ?>
                    </div>
                    <div class="col-sm-4 <?= $isRadio ?: 'hidden'; ?> answers">
                        <?= Html::label('Ответы', Html::getInputId($model, "[$i]question[responses][]")); ?>
                        <?php
                        if(!empty($model['responses']))
                            $c = count($model['responses']);
                        else
                            $c = 2;

                        for($j=0; $j<$c;$j++): ?>
                        <div class="row">
                            <div class="col-xs-4">
                                <a href="#" class="btn btn-primary add-answer"><i class="glyphicon glyphicon-plus"></i></a>
                                <a href="#" class="btn btn-warning remove-answer"><i class="glyphicon glyphicon-minus"></i></a>
                            </div>
                            <div class="col-xs-8">
                                <?= $form->field($model, "[$i]question[responses][$j]")->textInput()->label(false) ?>
                            </div>
                        </div>
                        <?php endfor; ?>

                    </div>
                    <div class="col-sm-1">
                        <?php echo Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['class' => 'btn btn-danger remove-question']); ?>
                    </div>
                </div>
                <hr/>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton($quest->isNewRecord ? 'Сохранить' : 'Сохранить', ['class' => $quest->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

        <?= Html::submitButton('Сохранить и добавить еще один', ['class' => 'btn btn-success', 'value' => 'yes', 'name' => 'one_more_please']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
