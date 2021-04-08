<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m210328_135518_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->string(50)->notNull(),
            'email' => $this->string(50)->notNull()->unique(),
            'password' => $this->string(100)->notNull(),
            'name' => $this->string(150)->notNull(),
            'type' => $this->integer(1)->notNull()->defaultValue(1),
            'password_reset_token' => $this->string(100),
            'authentication_key' => $this->string(100),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),
            'created_by' => $this->string(50)->notNull(),
            'updated_by' => $this->string(50),
        ]);
        $this->addPrimaryKey('pk_user', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
    }
}
