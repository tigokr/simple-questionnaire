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
 * This is the model class for table "questionnaire".
 *
 * @property integer $id
 * @property string $title
 * @property integer $timeout
 * @property string $type
 */
class Quest extends \yii\db\ActiveRecord
{

    const STATUS_ON = 'on';
    const STATUS_OFF = 'off';

    const TYPE_ALL = 'all-at-once';
    const TYPE_ONE = 'one-at-once';

    const TIMEOUT_UNLIMITED = null;
    const TIMEOUT_30M = '+30 minutes';
    const TIMEOUT_1H = '+1 hour';
    const TIMEOUT_3H = '+3 hours';
    const TIMEOUT_6H = '+6 hours';
    const TIMEOUT_12H = '+12 hours';
    const TIMEOUT_24H = '+24 hours';
    const TIMEOUT_3D = '+3 days';
    const TIMEOUT_1W = '+1 week';

    /**
     * @param null $v
     * @return array|null
     */
    public static function timeout($v = null, $ln = null)
    {
        switch ($ln) {
            case "val":
                $list = [
                    self::TIMEOUT_UNLIMITED => self::TIMEOUT_UNLIMITED,
                    self::TIMEOUT_30M => self::TIMEOUT_30M,
                    self::TIMEOUT_1H => self::TIMEOUT_1H,
                    self::TIMEOUT_3H => self::TIMEOUT_3H,
                    self::TIMEOUT_6H => self::TIMEOUT_6H,
                    self::TIMEOUT_12H => self::TIMEOUT_12H,
                    self::TIMEOUT_24H => self::TIMEOUT_24H,
                    self::TIMEOUT_3D => self::TIMEOUT_3D,
                    self::TIMEOUT_1W => self::TIMEOUT_1W,
                ];
                break;
            default:
                $list = [
                    self::TIMEOUT_UNLIMITED => 'Без ограничений',
                    self::TIMEOUT_30M => '30 минут',
                    self::TIMEOUT_1H => '1 час',
                    self::TIMEOUT_3H => '3 часа',
                    self::TIMEOUT_6H => '6 часов',
                    self::TIMEOUT_12H => '12 часов',
                    self::TIMEOUT_24H => '24 часа',
                    self::TIMEOUT_3D => '3 дня',
                    self::TIMEOUT_1W => '1 неделя',
                ];
                break;
        }

        if (is_null($v))
            return $list;

        return isset($list[$v]) ? $list[$v] : null;
    }

    /**
     * @param null $v
     * @return array|null
     */
    public static function status($v = null, $ln = null)
    {
        switch ($ln) {
            default:
                $list = [
                    self::STATUS_OFF => 'Выкл.',
                    self::STATUS_ON => 'Вкл.',
                ];
                break;
        }

        if (is_null($v))
            return $list;

        return isset($list[$v]) ? $list[$v] : null;
    }

    /**
     * @param null $v
     * @return array|null
     */
    public static function type($v = null, $ln = null)
    {
        switch ($ln) {
            default:
                $list = [
                    self::TYPE_ALL => 'Все сразу',
                    self::TYPE_ONE => 'По одному',
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
        return 'questionnaire';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            ['status', 'default', 'value'=>self::STATUS_ON],
            [['timeout'], 'safe'],
            [['title'], 'string', 'max' => 255],
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
            'title' => 'Название',
            'timeout' => 'Таймаут',
            'type' => 'Как показывать вопросы?',
        ];
    }

    public function beforeValidate()
    {
        parent::beforeValidate();

        if ($this->timeout) {
            $this->timeout = strtotime($this->timeout)-time();
        }

        return true;
    }

    public function afterFind()
    {
        parent::afterFind();

        if ($this->timeout !== null) {
            foreach (self::timeout(null, 'val') as $time) {
                if (time() + $this->timeout == strtotime($time))
                    return $this->timeout = self::timeout($time);
            }
        }

        return $this->timeout = self::TIMEOUT_UNLIMITED;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions(){
        return $this->hasMany(Question::className(), ['quest_id'=>'id']);
    }

    public function getUrl(){
        return ['/quest/view', 'id'=>$this->id];
    }

}