<?php

use yii\db\Migration;

/**
 * Class m200628_152717_update_to_1_2
 */
class m200628_152717_update_to_1_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add Conditional logic fields
        $this->addColumn('{{%addon_twilio}}', 'name', $this->string(255)->notNull()->after('status'));
        $this->addColumn('{{%addon_twilio}}', 'event', $this->integer()->notNull()->defaultValue(1)->after('name'));
        $this->addColumn('{{%addon_twilio}}', 'conditions', $this->text()->after('event'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%addon_twilio}}', 'event');
        $this->dropColumn('{{%addon_twilio}}', 'conditions');
        $this->dropColumn('{{%addon_twilio}}', 'name');
    }

}
