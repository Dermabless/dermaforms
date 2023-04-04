<?php

use yii\db\Migration;

/**
 * Class m200320_053029_upgrade_1
 */
class m200320_053029_upgrade_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%addon_twilio}}', 'created_by', $this->integer(11));
        $this->addColumn('{{%addon_twilio}}', 'updated_by', $this->integer(11));
        $this->addColumn('{{%addon_twilio}}', 'created_at', $this->integer());
        $this->addColumn('{{%addon_twilio}}', 'updated_at', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%addon_twilio}}', 'created_by');
        $this->dropColumn('{{%addon_twilio}}', 'updated_by');
        $this->dropColumn('{{%addon_twilio}}', 'created_at');
        $this->dropColumn('{{%addon_twilio}}', 'updated_at');
    }
}
