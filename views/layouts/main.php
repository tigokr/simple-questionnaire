<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title ? ($this->title . " | " . \Yii::$app->name) : \Yii::$app->name); ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => \Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Yii::$app->user->isGuest ?: Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            [
                'label' => 'Пригласить',
                'url' => ['/quest/invite'],
            ],
            [
                'label' => 'Опросы',
                'url' => ['/quest/index'],
            ],
            [
                'label' => 'Результаты',
                'url' => ['/results/index'],
            ],
            [
                'label' => 'Выход',
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post']
            ],
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?php if (Yii::$app->session->hasFlash('alert')): ?>
            <?php echo \yii\bootstrap\Alert::widget([
                'body' => \yii\helpers\ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                'options' => \yii\helpers\ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
            ]) ?>
        <?php endif; ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left"><a href="https://github.com/tigokr/simple-questionnaire">repo</a> &copy; <?= \Yii::$app->name; ?> <?= date('Y') ?></p>
        <p class="pull-right"><?php echo  !Yii::$app->user->isGuest?'': Html::a('Вход', ['/site/login']); ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
