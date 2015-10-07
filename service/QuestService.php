<?php

namespace app\service;
use app\events\QuestEvent;
use app\exceptions\BadQuestException;
use app\models\Quest;

/**
 * Created for simple-questionnaire
 * in extweb.org with love!
 * Artem Dekhtyar mail@artemd.ru
 * 07.10.2015
 */
class QuestService extends \yii\base\Component implements \yii\base\BootstrapInterface
{

    public $timeout = 3*60*60;

    const E_OPEN_NEW_QUEST = 'E_OPEN_NEW_QUEST';
    const E_SAVE_NEW_QUEST = 'E_SAVE_NEW_QUEST';
    const E_SAVE_QUEST = 'E_SAVE_QUEST';

    const GENDER_MALE = true;
    const GENDER_FEMALE = false;

    public function bootstrap($app)
    {

    }


    /**
     * @param Quest $model
     * @return bool
     */
    public function saveQuest(Quest $model){
        if($model->validate()) {
            $isNewRecord = $model->isNewRecord;

            $model->save();

            $event = new QuestEvent();
            $event->quest = $model;

            if($isNewRecord)
                \Yii::$app->trigger(self::E_SAVE_NEW_QUEST, $event);
            else
                \Yii::$app->trigger(self::E_SAVE_QUEST, $event);

            return true;
        }

        return false;
    }

    public function cloneQuest(Quest $model) {
        //....
    }

    /**
     * @param null $id
     * @param bool|true $allowNew
     * @return Quest
     * @throws BadQuestException
     */
    public function getQuest($id = null, $allowNew = true){

        if(empty($id) && $allowNew) {
            \Yii::$app->trigger(self::E_OPEN_NEW_QUEST);
            return new Quest();
        }

        if(!empty($id) && $model = Quest::find()->one($id))
            return $model;

        throw new BadQuestException('Такой анкеты не существует.');
    }

}

