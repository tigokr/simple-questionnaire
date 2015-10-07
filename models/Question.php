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
 * @property string $data
 */
class Question extends \yii\db\ActiveRecord
{

    const TYPE_DROPDOWN = 'dropdownList';
    const TYPE_TEXTINPUT = 'textInput';
    const TYPE_PASSWORD = 'password';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIOLIST = 'radioList';
    const TYPE_CHECKBOXLIST = 'checkboxList';
    const TYPE_WIDGET = 'widget';

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
            [['data'], 'required'],
            [['data'], 'string'],
            [['type'], 'string', 'max' => 40],
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
            'data' => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuest(){
        return $this->hasOne(Quest::className(), ['id'=>'quest_id']);
    }

}
