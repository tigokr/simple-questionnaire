<?php
/**
 * Created for simple-questionnaire
 * in extweb.org with love!
 * Artem Dekhtyar mail@artemd.ru
 * 07.10.2015
 */

namespace app\events;


use yii\base\Event;

class ResultEvent extends Event
{

    /*
     * @var Question
     */
    public $result;

}