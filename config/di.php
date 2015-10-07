<?php
/**
 * Created for simple-questionnaire
 * in extweb.org with love!
 * Artem Dekhtyar mail@artemd.ru
 * 07.10.2015
 */

\Yii::$container->set(\yii\widgets\Breadcrumbs::className(), [
    'homeLink' => [
        'label'=>'В начало',
        'url' => ['/'],
    ],
]);