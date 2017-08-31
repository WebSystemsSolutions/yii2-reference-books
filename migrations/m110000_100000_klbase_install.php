<?php

use yii\db\Migration;

class m110000_100000_klbase_install extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%klbase_bases}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(14)->notNull()->unique(),
            'title' => $this->string(36)->notNull(),
            'fields' => $this->text()->notNull(),
            'relation' => $this->string(14),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%klbase_data}}', [
            'id' => $this->primaryKey(),
            'base' => $this->string(14)->notNull(),
            'data_id' => $this->integer()->notNull(),
            'value' => $this->string(100),
            'title' => $this->string(100),
            'fields' => $this->text(),
            'relation' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('base', '{{%klbase_data}}', 'base');


    }

    public function down()
    {
        $this->dropTable('{{%klbase_bases}}');
        $this->dropTable('{{%klbase_data}}');
    }

}
