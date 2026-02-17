<?php

use yii\db\Migration;

/**
 * Handles adding resolution fields to table `{{%solicitud_tema_tesis}}`.
 * Adds motivo_resolucion and fecha_resolucion fields for managing STT resolutions.
 */
class m260217_040001_add_resolution_fields_to_solicitud_tema_tesis extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add motivo_resolucion field - stores reason for rejection or observations
        $this->addColumn('{{%solicitud_tema_tesis}}', 'motivo_resolucion', $this->text()->null());
        
        // Add fecha_resolucion field - stores resolution date
        $this->addColumn('{{%solicitud_tema_tesis}}', 'fecha_resolucion', $this->timestamp()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%solicitud_tema_tesis}}', 'fecha_resolucion');
        $this->dropColumn('{{%solicitud_tema_tesis}}', 'motivo_resolucion');
    }
}
