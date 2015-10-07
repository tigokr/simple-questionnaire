<?php

namespace app\service;

use app\events\QuestEvent;
use app\events\QuestionEvent;
use app\exceptions\BadQuestException;
use app\models\Quest;
use app\models\Question;
use yii\helpers\ArrayHelper;

/**
 * Created for simple-questionnaire
 * in extweb.org with love!
 * Artem Dekhtyar mail@artemd.ru
 * 07.10.2015
 */
class QuestService extends \yii\base\Component implements \yii\base\BootstrapInterface
{

    public $timeout = 3 * 60 * 60;

    const E_OPEN_NEW_QUEST = 'E_OPEN_NEW_QUEST';
    const E_SAVE_NEW_QUEST = 'E_SAVE_NEW_QUEST';
    const E_SAVE_QUEST = 'E_SAVE_QUEST';
    const E_QUEST_DELETE = 'E_QUEST_DELETE';

    const E_QUESTION_SAVED = 'E_QUESTION_SAVED';
    const E_QUESTION_DELETE = 'E_QUESTION_DELETE';

    const GENDER_MALE = true;
    const GENDER_FEMALE = false;

    public function bootstrap($app)
    {

    }


    /**
     * @param Quest $model
     * @return bool
     */
    public function saveQuest(Quest $model)
    {
        if ($model->validate()) {
            $isNewRecord = $model->isNewRecord;

            $model->save(false);

            $event = new QuestEvent();
            $event->quest = $model;

            if ($isNewRecord)
                \Yii::$app->trigger(self::E_SAVE_NEW_QUEST, $event);
            else
                \Yii::$app->trigger(self::E_SAVE_QUEST, $event);

            return true;
        }

        return false;
    }

    public function cloneQuest(Quest $model)
    {
        //....
    }

    /**
     * @param null $id
     * @param bool|true $allowNew
     * @return Quest
     * @throws BadQuestException
     */
    public function getQuest($id = null, $allowNew = true)
    {
        if (empty($id) && $allowNew) {
            \Yii::$app->trigger(self::E_OPEN_NEW_QUEST);
            return new Quest();
        }

        if (!empty($id) && $model = Quest::findOne($id))
            return $model;

        throw new BadQuestException('Такой анкеты не существует.');
    }


    /**
     * @param Quest $quest
     * @param Question[] $questions
     */
    public function saveQuestions(Quest $quest, $questions) {

        $old_ids = ArrayHelper::map($quest->questions, 'id', 'id');
        $new_ids = ArrayHelper::map($questions, 'id', 'id');

        $del_ids = array_diff($old_ids, $new_ids);

        if(!empty($del_ids)) {
            foreach($del_ids as $id) {
                $this->deleteQuestion($id);
            }
        }

        foreach($questions as $question) {
            $question->quest_id = $quest->id;
            if($question->save()) {
                \Yii::$app->trigger(self::E_QUESTION_SAVED);
            }
        }

        return $questions;
    }

    /**
     * @param null $id
     * @param bool|true $allowNew
     * @return Quest
     * @throws BadQuestException
     */
    public function deleteQuest($id)
    {
        $model = $this->getQuest($id);

        if ($result = $model->delete()) {

            $this->deleteQuestions($model);

            $event = new QuestEvent();
            $event->quest = $model;
            \Yii::$app->trigger(self::E_QUEST_DELETE, $event);
        }

        return $result;
    }

    /**
     * @param Quest $quest
     * @param $questions
     * @return array
     * @throws BadQuestException
     */
    public function loadQuestions(Quest $quest, $questions) {
        $data = [];

        foreach($questions as $question) {
            $model = $this->getQuestion($question['id']);
            $model->setAttributes($question);
            $model->quest_id = $quest->id;
            $data[] = $model;
        }

        return $data;
    }

    /**
     * @param Quest|integer $key
     * @param bool|true $allowNew
     * @return Question[]
     * @throws BadQuestException
     */
    public function getQuestions($key, $allowNew = true) {
        if(!$key instanceof Quest) {
            $quest = $this->getQuest($key);
        } else {
            $quest = $key;
        }

        if(!empty($quest->questions) || !$allowNew)
            return $quest->questions;

        $question = new Question();
        $question->quest_id = $quest->id;

        return [$question];
    }

    /**
     * @param null $id
     * @param bool|true $allowNew
     * @return Question|null|static
     * @throws BadQuestException
     */
    public function getQuestion($id = null, $allowNew = true) {
        if (empty($id) && $allowNew) {
            return new Question();
        }

        if (!empty($id) && $model = Question::findOne($id))
            return $model;

        throw new BadQuestException('Такого вопроса не существует.');
    }

    public function deleteQuestion($id){
        $model = $this->getQuestion($id);

        if ($result = $model->delete()) {

            $event = new QuestionEvent();
            $event->question = $model;
            \Yii::$app->trigger(self::E_QUESTION_DELETE, $event);
        }

        return $result;
    }


    /**
     * @param Quest $quest
     */
    public function deleteQuestions(Quest $quest){
        foreach($quest->questions as $question) {
            $event = new QuestionEvent();
            $event->quest = $question;
            \Yii::$app->trigger(self::E_QUESTION_DELETE, $event);
        }
    }

}

