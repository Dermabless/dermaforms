<?php

use yii\db\Migration;

/**
 * Class m210712_183202_update_to_1_3
 */
class m210712_183202_update_to_1_3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%addon_twilio}}', 'verification_service', $this->text()->after('text'));

        $this->createTable('{{%addon_twilio_item}}', [
            'id' => $this->primaryKey(),
            'twilio_id' => $this->integer(11),
            'form_id' => $this->integer(11),
            'phone_field' => $this->string(45),
            'button_field' => $this->string(45),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%addon_twilio_item}}');
        $this->dropColumn('{{%addon_twilio}}', 'verification_service');
    }
}
