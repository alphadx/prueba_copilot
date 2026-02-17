<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%historial_estado}}`.
 */
class m260217_010013_create_historial_estado_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%historial_estado}}', [
            'id' => $this->primaryKey(),
            // Polymorphic: can track STT or TT states
            'stt_id' => $this->integer()->null(),
            'tesis_id' => $this->integer()->null(),
            'estado_anterior' => $this->string(50)->null(),
            'estado_nuevo' => $this->string(50)->notNull(),
            'etapa' => $this->integer()->null(),
            'motivo' => $this->text()->null(),
            'usuario_id' => $this->integer()->notNull(),
            'fecha' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Create indexes
        $this->createIndex('idx-historial_estado-stt_id', '{{%historial_estado}}', 'stt_id');
        $this->createIndex('idx-historial_estado-tesis_id', '{{%historial_estado}}', 'tesis_id');
        $this->createIndex('idx-historial_estado-usuario_id', '{{%historial_estado}}', 'usuario_id');
        $this->createIndex('idx-historial_estado-fecha', '{{%historial_estado}}', 'fecha');

        // Note: SQLite does not support adding foreign keys after table creation
        // Foreign keys should be defined in the table schema if needed
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%historial_estado}}');
    }
}
