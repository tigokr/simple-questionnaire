<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Quest */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Анкеты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quest-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Вопросы', ['questions', 'quest_id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите это сделать?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'template' => "<tr><th width='50%'>{label}</th><td>{value}</td></tr>",
        'attributes' => [
            'id',
            'title',
            'timeout',
            [
                'attribute' => 'type',
                'value' => $model::type($model->type),
            ],
        ],
    ]) ?>

    <?php if ($model->questions): ?>
        <h2>Вопросы</h2>
        <?php foreach ($model->questions as $i => $question): ?>
            <?= DetailView::widget([
                'model' => $question,
                'template' => "<tr><th width='50%'>{label}</th><td>{value}</td></tr>",
                'attributes' => [
                    [
                        'label' => 'Вопрос №',
                        'value' => $i + 1,
                    ],
                    'text',
                    [
                        'label' => 'Ответы',
                        'format' => 'raw',
                        'value' => isset($question->question['responses'])?
                            '<ol><li>'.implode('</li><li>', $question->question['responses']).'</li></ol>'
                            :
                            null,
                        'visible' => $question->type == \app\models\Question::TYPE_RADIOLIST,
                    ]
                ],
            ]) ?>
        <?php endforeach; ?>
    <?php endif; ?>


</div>
