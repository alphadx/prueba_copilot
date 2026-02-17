<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tesis}}`.
 */
class m260217_010011_create_tesis_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tesis}}', [
            'id' => $this->primaryKey(),
            'stt_id' => $this->integer()->notNull()->unique(),
            // Category
            'categoria_id' => $this->integer()->null(),
            'subcategoria_id' => $this->integer()->null(),
            // Assigned professors
            'profesor_guia_id' => $this->integer()->notNull(),
            'profesor_revisor1_id' => $this->integer()->null(),
            'profesor_revisor2_id' => $this->integer()->null(),
            // Dynamic stage system
            'total_etapas' => $this->integer()->notNull()->defaultValue(1),
            'etapa_actual' => $this->integer()->notNull()->defaultValue(1),
            // State
            'estado' => $this->string(50)->notNull()->defaultValue('Aceptada'),
            // Resolution
            'resolucion_motivo' => $this->text()->null(),
            // Timestamps
            'fecha_aceptacion' => $this->timestamp()->null(),
            'fecha_ultima_actualizacion' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null(),
        ]);

        // Create indexes
        $this->createIndex('idx-tesis-stt_id', '{{%tesis}}', 'stt_id');
        $this->createIndex('idx-tesis-categoria_id', '{{%tesis}}', 'categoria_id');
        $this->createIndex('idx-tesis-subcategoria_id', '{{%tesis}}', 'subcategoria_id');
        $this->createIndex('idx-tesis-profesor_guia_id', '{{%tesis}}', 'profesor_guia_id');
        $this->createIndex('idx-tesis-estado', '{{%tesis}}', 'estado');

        // Add foreign keys
        $this->addForeignKey(
            'fk-tesis-stt_id',
            '{{%tesis}}',
            'stt_id',
            '{{%solicitud_tema_tesis}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-tesis-categoria_id',
            '{{%tesis}}',
            'categoria_id',
            '{{%categoria}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-tesis-subcategoria_id',
            '{{%tesis}}',
            'subcategoria_id',
            '{{%subcategoria}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-tesis-profesor_guia_id',
            '{{%tesis}}',
            'profesor_guia_id',
            '{{%profesor}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-tesis-profesor_revisor1_id',
            '{{%tesis}}',
            'profesor_revisor1_id',
            '{{%profesor}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-tesis-profesor_revisor2_id',
            '{{%tesis}}',
            'profesor_revisor2_id',
            '{{%profesor}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-tesis-profesor_revisor2_id', '{{%tesis}}');
        $this->dropForeignKey('fk-tesis-profesor_revisor1_id', '{{%tesis}}');
        $this->dropForeignKey('fk-tesis-profesor_guia_id', '{{%tesis}}');
        $this->dropForeignKey('fk-tesis-subcategoria_id', '{{%tesis}}');
        $this->dropForeignKey('fk-tesis-categoria_id', '{{%tesis}}');
        $this->dropForeignKey('fk-tesis-stt_id', '{{%tesis}}');
        $this->dropTable('{{%tesis}}');
    }
}
