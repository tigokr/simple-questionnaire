<?php
/**
 * Created for simple-questionnaire
 * in extweb.org with love!
 * Artem Dekhtyar mail@artemd.ru
 * 08.10.2015
 */

use yii\bootstrap\Html;

/* @var \yii\web\View $this */
/* @var \app\models\Quest $quest */
/* @var \app\models\Result $model */

$this->title = $quest->title;

?>

<div class="questionnaire-run">
    <h1><?= Html::encode($this->title); ?></h1>

    <p>Спасибо за то, что уделили нам время! В ближайшее время мы обязательно с Вами свяжемся!</p>

</div>
