<?php

namespace app\service;

use app\events\QuestEvent;
use app\events\QuestionEvent;
use app\events\ResultEvent;
use app\exceptions\BadQuestException;
use app\models\Quest;
use app\models\Question;
use app\models\Result;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\widgets\DetailView;

/**
 * Created for simple-questionnaire
 * in extweb.org with love!
 * Artem Dekhtyar mail@artemd.ru
 * 07.10.2015
 */
class QuestService extends \yii\base\Component implements \yii\base\BootstrapInterface
{
    const E_OPEN_NEW_QUEST = 'E_OPEN_NEW_QUEST';
    const E_SAVE_NEW_QUEST = 'E_SAVE_NEW_QUEST';
    const E_SAVE_QUEST = 'E_SAVE_QUEST';
    const E_QUEST_DELETE = 'E_QUEST_DELETE';

    const E_QUESTION_SAVED = 'E_QUESTION_SAVED';
    const E_QUESTION_DELETE = 'E_QUESTION_DELETE';

    const E_INVATE_SEND = 'E_INVATE_SEND';
    const E_QUEST_START = 'E_QUEST_START';
    const E_QUEST_TIMEOUT = 'E_QUEST_TIMEOUT';
    const E_QUEST_FINISH = 'E_QUEST_FINISH';
    const E_RESULT_DELETE = 'E_RESULT_DELETE';

    public function bootstrap($app)
    {
    }


    public function startQuest($key)
    {
        $result = $this->getResultByKey($key);
        if(
            (!empty($result->quest->timeout) && time() - $result->start_at > $result->quest->timeout) ||
            ($result->quest->status == Quest::STATUS_OFF) ||
            !empty($result->finish_at)
        ) {

            $event = new ResultEvent();
            $event->result = $result;
            \Yii::$app->trigger(self::E_QUEST_TIMEOUT, $event);

            throw new NotFoundHttpException();
        }

        $result->updateAttributes(['start_at'=>time()]);

        $event = new ResultEvent();
        $event->result = $result;
        \Yii::$app->trigger(self::E_INVATE_SEND, $event);

        return $result;
    }

    /**
     * @param $key
     * @return Result
     * @throws NotFoundHttpException
     */
    public function getResultByKey($key) {

        if (($model = Result::find()->where(['key'=>$key])->one()) && empty($model->finish_at)) {
            return $model;
        }

        throw new NotFoundHttpException();
    }

    public function saveResult(Result $result) {

        $result->finish_at = time();
        $result->scenario = 'quest';

        if($result->save()) {

            $attributes = [
                'id',
                //'key',
                'email:email',
                [
                    'attribute' => 'quest_id',
                    'format' => 'html',
                    'value' => $result->quest?$result->quest->title:null,
                ],
                'first_name',
                'second_name',
                [
                    'attribute'=> 'gender',
                    'value' => $result->gender?'Мужчина':'Женщина',
                ],
                'birthday:date',
                'location',
                'invated_at:datetime',
                'start_at:datetime',
                'finish_at:datetime',
            ];

            if(!empty($result->results)) {
                $attributes[] = ['label' => 'Ответы на вопросы', 'value'=>''];
                foreach ($result->results as $r) {
                    $attributes[] = ['label' => $r['question'], 'value'=>$r['response']];
                }
            }

            $html = DetailView::widget([
                'model' => $result,
                'attributes' => $attributes,
            ]);

            /** @var ResultEvent $event */
            $mail = \Yii::$app->mailer->compose();

            $mail
                ->setTo(\Yii::$app->params['adminEmail'])
                ->setFrom(\Yii::$app->params['robot'])
                ->setSubject('Резльтаты анкетирования')
                ->setHtmlBody($html)
                ->send();

            $event = new ResultEvent();
            $event->result = $result;
            \Yii::$app->trigger(self::E_QUEST_FINISH, $event);

            return true;
        }

        return false;
    }

    public function cleanResults(){
        $results = Result::find()->all();

        foreach($results as $result) {
            if($result->delete()) {
                $event = new ResultEvent();
                $event->result = $result;
                \Yii::$app->trigger(self::E_RESULT_DELETE, $event);
            }
        }

        return true;
    }

    /**
     * @param $quest_id
     * @param $email
     * @return bool|string
     * @throws BadQuestException
     */
    public function invate($quest_id, $email)
    {
        $quest = $this->getQuest($quest_id, false);

        $result = new Result();
        $result->key = md5($quest_id . $email . time());
        $result->email = $email;
        $result->quest_id = $quest_id;

        /** @var ResultEvent $event */
        $mail = \Yii::$app->mailer->compose();

        $url = \Yii::$app->urlManager->createAbsoluteUrl(['/quest/start', 'key' => $result->key]);

        $mail_result = $mail
            ->setTo($result->email)
            ->setFrom(\Yii::$app->params['robot'])
            ->setSubject('Вы приглашены на online-собеседование от комании ' . \Yii::$app->name)
            ->setHtmlBody(nl2br("Приглашаем Вас на online собеседование в компанию " . \Yii::$app->name . "
                    Для начала анкетирования Вам необходимо перейти по ссылке <a href='$url'>$url</a>
                    По окончанию анкетирования сохраните результаты. С Вами свяжутся по окончании проверки Ваших результатов.
                "))
            ->send();

        if ($mail_result) {
            $result->save();

            $event = new ResultEvent();
            $event->result = $result;
            \Yii::$app->trigger(self::E_INVATE_SEND, $event);

            return $result->key;
        }


        return false;
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
    public function saveQuestions(Quest $quest, $questions)
    {

        $old_ids = ArrayHelper::map($quest->questions, 'id', 'id');
        $new_ids = ArrayHelper::map($questions, 'id', 'id');

        $del_ids = array_diff($old_ids, $new_ids);

        if (!empty($del_ids)) {
            foreach ($del_ids as $id) {
                $this->deleteQuestion($id);
            }
        }

        foreach ($questions as $question) {
            $question->quest_id = $quest->id;
            if ($question->save()) {
                \Yii::$app->trigger(self::E_QUESTION_SAVED);
            }
        }

        return true;
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

        $model->updateAttributes(['status'=>Quest::STATUS_OFF]);

        $event = new QuestEvent();
        $event->quest = $model;
        \Yii::$app->trigger(self::E_QUEST_DELETE, $event);

        return true;
    }

    /**
     * @param Quest $quest
     * @param $questions
     * @return array
     * @throws BadQuestException
     */
    public function loadQuestions(Quest $quest, $questions)
    {
        $data = [];

        if(!empty($questions))
            foreach ($questions as $question) {
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
    public function getQuestions($key, $allowNew = true)
    {
        if (!$key instanceof Quest) {
            $quest = $this->getQuest($key);
        } else {
            $quest = $key;
        }

        if (!empty($quest->questions) || !$allowNew)
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
    public function getQuestion($id = null, $allowNew = true)
    {
        if (empty($id) && $allowNew) {
            return new Question();
        }

        if (!empty($id) && $model = Question::findOne($id))
            return $model;

        throw new BadQuestException('Такого вопроса не существует.');
    }

    public function deleteQuestion($id)
    {
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
    public function deleteQuestions(Quest $quest)
    {
        foreach ($quest->questions as $question) {
            $event = new QuestionEvent();
            $event->quest = $question;
            \Yii::$app->trigger(self::E_QUESTION_DELETE, $event);
        }
    }

}

