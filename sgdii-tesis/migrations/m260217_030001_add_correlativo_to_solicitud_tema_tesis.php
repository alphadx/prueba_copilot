<?php

use yii\db\Migration;

/**
 * Adds correlativo field to solicitud_tema_tesis table
 */
class m260217_030001_add_correlativo_to_solicitud_tema_tesis extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%solicitud_tema_tesis}}', 'correlativo', $this->string(20)->null()->after('id'));
        $this->createIndex('idx-stt-correlativo', '{{%solicitud_tema_tesis}}', 'correlativo', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-stt-correlativo', '{{%solicitud_tema_tesis}}');
        $this->dropColumn('{{%solicitud_tema_tesis}}', 'correlativo');
    }
}
