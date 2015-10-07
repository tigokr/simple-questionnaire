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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'key',
            'email:email',
            'first_name',
            'second_name',
            'gender:boolean',
            'birthday',
            'location',
            'start_at',
            'finish_at',
            'quest_id',
            'data:ntext',
        ],
    ]) ?>

</div>
