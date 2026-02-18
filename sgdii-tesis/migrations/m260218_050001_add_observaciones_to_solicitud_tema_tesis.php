<?php

use yii\db\Migration;

/**
 * Handles adding observaciones column to table `{{%solicitud_tema_tesis}}`.
 * This provides a dedicated field for observations when accepting STTs with observations,
 * separate from the general motivo_resolucion field.
 */
class m260218_050001_add_observaciones_to_solicitud_tema_tesis extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add observaciones field - specifically for observations when accepting with observations
        $this->addColumn('{{%solicitud_tema_tesis}}', 'observaciones', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%solicitud_tema_tesis}}', 'observaciones');
    }
}
