<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%document}}`.
 */
class m210403_134623_create_document_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('document', [
            'id' => $this->string(50)->notNull(),
            'no' => $this->string(30)->notNull(),
            'name' => $this->string(200)->notNull(),
            'year' => $this->integer(4)->notNull(),
            'category' => $this->string(50)->notNull(),
            'area' => $this->string(50),
            'pic' => $this->string(200)->notNull(),
            'status' => $this->integer(1)->notNull()->defaultValue(0),
            'filename' => $this->string(250)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),
            'created_by' => $this->string(50)->notNull(),
            'updated_by' => $this->string(50),
        ]);

        $this->addPrimaryKey('pk-document', 'document', 'id');
        $this->createIndex('uq-document', 'document', ['no', 'category', 'area'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('document');
    }
}
