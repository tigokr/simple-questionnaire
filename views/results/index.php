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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'email:email',
            'first_name',
            'second_name',
            'gender:boolean',
            'birthday',
            'location',
            // 'start_at',
            // 'finish_at',
            //  'quest_id',
            // 'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
