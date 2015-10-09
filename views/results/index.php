<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ResultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Результаты анкетирования';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="result-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p class="text-right">
        <?= Html::a('Очистить результаты', ['clean'], ['class' => 'btn btn-danger', 'data' => [
            'confirm' => 'Вы уверены, что хотите это сделать?',
            'method' => 'post',
        ],]) ?>
    </p>

    <?php 
	$dataProvider->query->orderBy('invated_at desc');
	echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'quest_id',
                'format' => 'html',
                'value' => function ($model) {
                    return $model->quest ? \yii\helpers\Html::a($model->quest->title, $model->quest->url) : null;
                },
                'filter' => \yii\helpers\ArrayHelper::map(\app\models\Quest::find()->all(), 'id', 'title'),
            ],
            'email:email',
            'phone',
            'first_name',
            'second_name',
//            'gender:boolean',
            'location',
//            'invated_at:datetime',
//            'start_at:date',
            'finish_at:date',
            //  'quest_id',
            // 'data:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}'
            ],
        ],
    ]); ?>

</div>
