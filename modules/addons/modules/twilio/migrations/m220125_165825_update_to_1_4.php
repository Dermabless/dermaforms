<?php

use yii\db\Migration;

/**
 * Class m220125_165825_update_to_1_4
 */
class m220125_165825_update_to_1_4 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%addon_twilio}}', 'api_key', $this->string(255)->null());
        $this->alterColumn('{{%addon_twilio}}', 'api_secret', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%addon_twilio}}', 'api_key', $this->string(255)->notNull());
        $this->alterColumn('{{%addon_twilio}}', 'api_secret', $this->string(255)->notNull());
    }

}
