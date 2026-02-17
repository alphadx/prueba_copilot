<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%solicitud_tema_tesis}}`.
 */
class m260217_010009_create_solicitud_tema_tesis_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%solicitud_tema_tesis}}', [
            'id' => $this->primaryKey(),
            // Origin & Course
            'origen_id' => $this->integer()->notNull(),
            'profesor_curso_id' => $this->integer()->notNull(),
            'nota' => $this->decimal(2, 1)->notNull(),
            // Modality
            'modalidad_id' => $this->integer()->notNull(),
            // Proposed professors
            'profesor_guia_propuesto_id' => $this->integer()->null(),
            'profesor_revisor1_propuesto_id' => $this->integer()->null(),
            'profesor_revisor2_propuesto_id' => $this->integer()->null(),
            // Company data
            'empresa_id' => $this->integer()->null(),
            // Thesis info
            'titulo' => $this->string(500)->notNull(),
            'documento_path' => $this->string(500)->null(),
            // State
            'estado' => $this->string(50)->notNull()->defaultValue('Solicitada'),
            // Timestamps
            'fecha_creacion' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null(),
        ]);

        // Create indexes
        $this->createIndex('idx-stt-origen_id', '{{%solicitud_tema_tesis}}', 'origen_id');
        $this->createIndex('idx-stt-profesor_curso_id', '{{%solicitud_tema_tesis}}', 'profesor_curso_id');
        $this->createIndex('idx-stt-modalidad_id', '{{%solicitud_tema_tesis}}', 'modalidad_id');
        $this->createIndex('idx-stt-empresa_id', '{{%solicitud_tema_tesis}}', 'empresa_id');
        $this->createIndex('idx-stt-estado', '{{%solicitud_tema_tesis}}', 'estado');

        // Note: SQLite does not support adding foreign keys after table creation
        // Foreign keys should be defined in the table schema if needed
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%solicitud_tema_tesis}}');
    }
}
