<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%area}}`.
 */
class m210403_134558_create_area_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('area', [
            'id' => $this->string(50)->notNull(),
            'name' => $this->string(200)->notNull(),
            'parent' => $this->string(50),
        ]);

        $this->addPrimaryKey('pk-area', 'area', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('area');
    }
}
