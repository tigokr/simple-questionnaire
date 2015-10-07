<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <h1><?= Html::encode($this->title) ?></h1>
        <h2>Ох... Вы что то сломали, не делайте так больше!</h2>

</div>
