<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%profesor}}`.
 */
class m260217_010002_create_profesor_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%profesor}}', [
            'id' => $this->primaryKey(),
            'rut' => $this->string(12)->notNull()->unique(),
            'nombre' => $this->string(255)->notNull(),
            'correo' => $this->string(255)->null(),
            'telefono' => $this->string(20)->null(),
            'especialidad' => $this->string(255)->null(),
            'es_comision_evaluadora' => $this->integer()->defaultValue(0),
            'user_id' => $this->integer()->null(),
            'activo' => $this->integer()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null(),
        ]);

        $this->createIndex(
            'idx-profesor-rut',
            '{{%profesor}}',
            'rut'
        );

        $this->createIndex(
            'idx-profesor-user_id',
            '{{%profesor}}',
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
        $this->dropTable('{{%profesor}}');
    }
}
