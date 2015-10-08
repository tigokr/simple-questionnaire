<?php

namespace app\models;

use Yii;
use yii\base\Model;

class InviteForm extends Model
{
    public $email;
    public $quest_id;

    public function rules()
    {
        return [
            [['email', 'quest_id'], 'required'],
            ['email', 'email'],
            ['quest_id', 'integer'],
        ];
    }

}
