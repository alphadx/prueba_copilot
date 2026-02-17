<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%stt_alumno}}`.
 */
class m260217_010010_create_stt_alumno_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%stt_alumno}}', [
            'id' => $this->primaryKey(),
            'stt_id' => $this->integer()->notNull(),
            'alumno_id' => $this->integer()->notNull(),
            'carrera_malla_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Create indexes
        $this->createIndex('idx-stt_alumno-stt_id', '{{%stt_alumno}}', 'stt_id');
        $this->createIndex('idx-stt_alumno-alumno_id', '{{%stt_alumno}}', 'alumno_id');
        $this->createIndex('idx-stt_alumno-carrera_malla_id', '{{%stt_alumno}}', 'carrera_malla_id');

        // Create unique constraint for stt_id + alumno_id
        $this->createIndex(
            'uq-stt_alumno-stt_id-alumno_id',
            '{{%stt_alumno}}',
            ['stt_id', 'alumno_id'],
            true
        );

        // Note: SQLite does not support adding foreign keys after table creation.
        // For production, foreign keys should be defined in the table schema using FOREIGN KEY constraints,
        // or enforced at the application level. For this prototype with SQLite, indexes are sufficient.
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%stt_alumno}}');
    }
}
