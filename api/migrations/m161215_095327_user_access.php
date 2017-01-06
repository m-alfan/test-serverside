<?php

use yii\db\Migration;

class m161215_095327_user_access extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%user}}', [
            'id'                   => $this->primaryKey(),
            'username'             => $this->string(32)->notNull()->unique(),
            'auth_key'             => $this->string(32)->notNull(),
            'password_hash'        => $this->string()->notNull(),
            'password_reset_token' => $this->string(),
            'email'                => $this->string()->notNull(),
            'status'               => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at'           => $this->integer(),
            'updated_at'           => $this->integer(),
        ], $tableOptions);
        $this->createTable('{{%user_token}}', [
            'user_id' => $this->integer(),
            'token'   => $this->string()->notNull()->unique(),
            'expire'  => $this->integer(),
            'PRIMARY KEY ([[user_id]])',
            'FOREIGN KEY ([[user_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_token}}');
        $this->dropTable('{{%user}}');
    }
}
