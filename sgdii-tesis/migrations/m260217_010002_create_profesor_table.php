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

        // Add foreign key to user table
        $this->addForeignKey(
            'fk-profesor-user_id',
            '{{%profesor}}',
            'user_id',
            '{{%user}}',
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
        $this->dropForeignKey('fk-profesor-user_id', '{{%profesor}}');
        $this->dropTable('{{%profesor}}');
    }
}
