<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alumno}}`.
 */
class m260217_010003_create_alumno_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%alumno}}', [
            'id' => $this->primaryKey(),
            'rut' => $this->string(12)->notNull()->unique(),
            'nombre' => $this->string(255)->notNull(),
            'correo' => $this->string(255)->null(),
            'telefono' => $this->string(20)->null(),
            'carrera_malla_id' => $this->integer()->notNull(),
            'tipo_ingreso' => $this->string(50)->defaultValue('PAES'),
            'anio_ingreso' => $this->integer()->null(),
            'user_id' => $this->integer()->null(),
            'activo' => $this->integer()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null(),
        ]);

        $this->createIndex(
            'idx-alumno-rut',
            '{{%alumno}}',
            'rut'
        );

        $this->createIndex(
            'idx-alumno-carrera_malla_id',
            '{{%alumno}}',
            'carrera_malla_id'
        );

        $this->createIndex(
            'idx-alumno-user_id',
            '{{%alumno}}',
            'user_id'
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
        $this->dropTable('{{%alumno}}');
    }
}
