<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Result */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Результаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="result-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите это сделать?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php

    $attributes = [
        'id',
        'key',
        'email:email',
        [
            'attribute' => 'quest_id',
            'format' => 'html',
            'value' => $model->quest?Html::a($model->quest->title, $model->quest->url):null,
        ],
        'first_name',
        'second_name',
        [
            'attribute'=> 'gender',
            'value' => $model->gender?'Мужчина':'Женщина',
        ],
        'birthday:date',
        'location',
        'invated_at:datetime',
        'start_at:datetime',
        'finish_at:datetime',
    ];

    if(!empty($model->results)) {
        $attributes[] = ['label' => 'Ответы на вопросы', 'value'=>''];
        foreach ($model->results as $r) {
            $attributes[] = ['label' => $r['question'], 'value'=>$r['response']];
        }
    }

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
    ]) ?>

</div>
