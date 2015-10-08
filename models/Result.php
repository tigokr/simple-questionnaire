<?php
/**
 * Created for simple-questionnaire
 * in extweb.org with love!
 * Artem Dekhtyar mail@artemd.ru
 * 07.10.2015
 */

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

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

    public $results;

    const GENDER_MALE = true;
    const GENDER_FEMALE = false;

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
            [['key', 'email'], 'required'],
            [['first_name', 'second_name', 'location', 'birthday', 'gender', 'phone'], 'required', 'on'=>'quest'],
            [['gender'], 'boolean'],
            [['start_at', 'finish_at', 'quest_id'], 'integer'],
            [['data'], 'string'],
            [['key', 'phone'], 'string', 'max' => 40],
            [['email', 'first_name', 'second_name'], 'string', 'max' => 80],
            [['location'], 'string', 'max' => 255],
            [['birthday'], 'filter', 'filter' => 'strtotime'],
            [['birthday'], 'default', 'value' => time()],

            ['results', 'safe'],
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
            'location' => 'Место проживания',
            'phone' => 'Телефон',
            'invated_at' => 'Дата отправки приглашения',
            'start_at' => 'Начало тестирования',
            'finish_at' => 'Окончание тестирования',
            'quest_id' => 'Анкета',
            'data' => 'Data',
        ];
    }

    public function behaviors(){
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'invated_at',
                'updatedAtAttribute' => false,
            ]
        ];
    }


    /**
     * @return Quest
     */
    public function getQuest(){
        return $this->hasOne(Quest::className(), ['id'=>'quest_id']);
    }

    public function beforeValidate(){
        parent::beforeValidate();

        $this->data = \yii\helpers\Json::encode($this->results);
        return true;
    }

    public function afterFind(){
        parent::afterFind();
        $this->results = \yii\helpers\Json::decode($this->data);
    }

}