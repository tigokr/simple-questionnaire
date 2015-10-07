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
        <?= Html::encode($this->title) ?> <?= nl2br(Html::encode($message)) ?>
    </div>


    <h2>Ох... Вы опять что то сломали, не делайте так больше...</h2>

</div>
