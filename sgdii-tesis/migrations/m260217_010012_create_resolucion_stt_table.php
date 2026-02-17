<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%resolucion_stt}}`.
 */
class m260217_010012_create_resolucion_stt_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%resolucion_stt}}', [
            'id' => $this->primaryKey(),
            'stt_id' => $this->integer()->notNull(),
            'tipo' => $this->string(50)->notNull(),
            'motivo' => $this->text()->notNull(),
            'usuario_id' => $this->integer()->notNull(),
            'fecha_resolucion' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Create indexes
        $this->createIndex('idx-resolucion_stt-stt_id', '{{%resolucion_stt}}', 'stt_id');
        $this->createIndex('idx-resolucion_stt-usuario_id', '{{%resolucion_stt}}', 'usuario_id');
        $this->createIndex('idx-resolucion_stt-tipo', '{{%resolucion_stt}}', 'tipo');

        // Add foreign keys
        $this->addForeignKey(
            'fk-resolucion_stt-stt_id',
            '{{%resolucion_stt}}',
            'stt_id',
            '{{%solicitud_tema_tesis}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-resolucion_stt-usuario_id',
            '{{%resolucion_stt}}',
            'usuario_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-resolucion_stt-usuario_id', '{{%resolucion_stt}}');
        $this->dropForeignKey('fk-resolucion_stt-stt_id', '{{%resolucion_stt}}');
        $this->dropTable('{{%resolucion_stt}}');
    }
}
