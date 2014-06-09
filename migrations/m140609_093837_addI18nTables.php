<?php

use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\db\Schema;

class m140609_093837_addI18nTables extends Migration
{
    public function up()
    {
        $i18n = Yii::$app->getI18n();
        if (!isset($i18n->sourceMessageTable) || !isset($i18n->messageTable)) {
            throw new InvalidConfigException('You should configure i18n component');
        }
        $sourceMessageTable = $i18n->sourceMessageTable;
        $messageTable = $i18n->messageTable;

        $this->createTable($sourceMessageTable, [
            'id' => Schema::TYPE_PK,
            'category' => 'VARCHAR(32) NULL',
            'message' => 'TEXT NULL'
        ]);

        $this->createTable($messageTable, [
            'id' => Schema::TYPE_INTEGER . ' NOT NULL default 0',
            'language' => 'VARCHAR(16) NOT NULL default ""',
            'translation' => 'TEXT NULL'
        ]);
        $this->addPrimaryKey('id', $messageTable, ['id', 'language']);
        $this->addForeignKey('fk_source_message_message', $messageTable, 'id', $sourceMessageTable, 'id', 'cascade');
    }

    public function down()
    {
        echo "m140609_093837_addI18nTables cannot be reverted.\n";
        return false;
    }
}