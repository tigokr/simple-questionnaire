<?php

use yii\db\Migration;
use app\service\QuestService;
use app\models\Quest;
use app\models\Question;
use app\models\Result;

class m151007_163229_questionnaire extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(Quest::tableName(), [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'timeout' => $this->integer()->defaultValue(null),
            'type' => $this->string(40)->notNull()->defaultValue(Quest::TYPE_ALL),
            'status' => $this->string(40)->notNull()->defaultValue(Quest::STATUS_ON),
        ], $tableOptions);

        $this->createTable(Question::tableName(), [
            'id' => $this->primaryKey(),
            'quest_id' => $this->integer(),
            'type' => $this->string(40)->notNull()->defaultValue(Question::TYPE_TEXTINPUT),
            'text' => $this->text()->notNull(),
            'data' => $this->text()->notNull(),
        ], $tableOptions);
        $this->createIndex('Question_idx_quest_id', Question::tableName(), 'quest_id');

        $this->createTable(Result::tableName(), [
            'id' => $this->primaryKey(),
            'key' => $this->string(40)->notNull(),
            'email' => $this->string(80)->notNull(),

            'first_name' => $this->string(80),
            'second_name' => $this->string(80),
            'gender' => $this->boolean()->defaultValue(Result::GENDER_MALE),
            'birthday' => $this->integer(),
            'location' => $this->string(255),
            'phone' => $this->string(40),

            'start_at' => $this->integer(),
            'finish_at' => $this->integer(),
            'quest_id' => $this->integer()->defaultValue(null),
            'data' => $this->text(),
            'invated_at' => $this->integer(),
        ], $tableOptions);
        $this->createIndex('Result_idx_quest_id', Result::tableName(), 'quest_id');
        $this->createIndex('Result_idx_key', Result::tableName(), 'key');
        $this->createIndex('Result_idx_email', Result::tableName(), 'email');

        if ($this->db->driverName === 'mysql') {
            // add fk
        }
    }

    public function down()
    {

        if ($this->db->driverName === 'mysql') {
            // drop fk
        }

        $this->dropTable(Quest::tableName());
        $this->dropTable(Question::tableName());
        $this->dropTable(Result::tableName());
    }
}
