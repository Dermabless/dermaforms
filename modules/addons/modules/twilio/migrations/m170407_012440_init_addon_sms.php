<?php

use yii\db\Migration;

/**
 * Class m170407_012440_init_addon_sms
 */
class m170407_012440_init_addon_sms extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%addon_twilio}}', [
            'id' => $this->primaryKey(),
            'form_id' => $this->integer(11)->notNull(),
            'platform' => $this->string(255)->notNull()->defaultValue('twilio'),
            'api_key' => $this->string(255)->notNull(),
            'api_secret' => $this->string(255)->notNull(),
            'to' => $this->string(255),
            'from' => $this->string(255),
            'status' => $this->boolean()->notNull()->defaultValue(0),
            'text' => $this->text(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%addon_twilio}}');
    }
}
