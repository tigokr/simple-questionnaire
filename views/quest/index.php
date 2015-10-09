<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\QuestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Анкеты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quest-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать анкету', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'title',
            [
                'attribute' => 'timeout',
                'filter' => \app\models\Quest::timeout(),
            ],
            [
                'attribute' => 'type',
                'value' => function($model) {
                    return $model::type($model->type);
                },
                'filter' => \app\models\Quest::type(),
            ],


            [
                'class' => \yii\grid\ActionColumn::className(),
                'template' => '{questions} {view} {update} {delete}',
                'buttons' => [
                    'questions' => function($key, $model, $url) {
                        return \yii\bootstrap\Html::a('<i class="glyphicon glyphicon-question-sign"></i>', ['questions', 'quest_id'=>$model->id]);
                    }
                ]

            ],
        ],
    ]); ?>

</div>
