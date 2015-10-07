<?php
/**
 * Created for simple-questionnaire
 * in extweb.org with love!
 * Artem Dekhtyar mail@artemd.ru
 * 07.10.2015
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "question".
 *
 * @property integer $id
 * @property integer $quest_id
 * @property string $type
 * @property string $text
 * @property string $data
 */
class Question extends \yii\db\ActiveRecord
{

    public $question;

    const TYPE_TEXTINPUT = 'textInput';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_RADIOLIST = 'radioList';
    const TYPE_FILE = 'fileInput';

    /**
     * @param null $v
     * @return array|null
     */
    public static function type($v = null, $ln = null)
    {
        switch ($ln) {
            default:
                $list = [
                    self::TYPE_TEXTINPUT => 'Краткий ответ',
                    self::TYPE_TEXTAREA => 'Объёмный ответ',
                    self::TYPE_RADIOLIST => 'Выбор варинатов ответа',
                    self::TYPE_FILE => 'Файл',
                ];
                break;
        }

        if (is_null($v))
            return $list;

        return isset($list[$v]) ? $list[$v] : null;
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quest_id'], 'integer'],
            [['text'], 'required'],
            [['data'], 'string'],
            [['type'], 'string', 'max' => 40],
            [['question'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quest_id' => 'Quest ID',
            'type' => 'Тип',
            'text' => 'Вопрос',
            'data' => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuest()
    {
        return $this->hasOne(Quest::className(), ['id' => 'quest_id']);
    }

    public function beforeValidate(){
        parent::beforeValidate();

        if($this->type == self::TYPE_RADIOLIST && isset($this->question['responses'])) {
            $this->question['responses'] = array_values($this->question['responses']);
        } else {
            unset($this->question['responses']);
        }

        $this->data = \yii\helpers\Json::encode($this->question);
        return true;
    }

    public function afterFind(){
        parent::afterFind();
        $this->question = \yii\helpers\Json::decode($this->data);
    }

}
