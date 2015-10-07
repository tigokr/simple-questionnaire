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
 * This is the model class for table "result".
 *
 * @property integer $id
 * @property string $key
 * @property string $email
 * @property string $first_name
 * @property string $second_name
 * @property boolean $gender
 * @property integer $birthday
 * @property string $location
 * @property integer $start_at
 * @property integer $finish_at
 * @property integer $quest_id
 * @property string $data
 */
class Result extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'email', 'first_name', 'second_name'], 'required'],
            [['gender'], 'boolean'],
            [['birthday', 'start_at', 'finish_at', 'quest_id'], 'integer'],
            [['data'], 'string'],
            [['key'], 'string', 'max' => 32],
            [['email', 'first_name', 'second_name'], 'string', 'max' => 80],
            [['location'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'email' => 'Email',
            'first_name' => 'Имя',
            'second_name' => 'Фамилия',
            'gender' => 'Пол',
            'birthday' => 'День рождения',
            'location' => 'Адрес',
            'start_at' => 'Start At',
            'finish_at' => 'Finish At',
            'quest_id' => 'Quest ID',
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