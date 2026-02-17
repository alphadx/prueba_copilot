<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%empresa}}`.
 */
class m260217_010004_create_empresa_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%empresa}}', [
            'id' => $this->primaryKey(),
            'rut' => $this->string(12)->notNull()->unique(),
            'nombre' => $this->string(255)->notNull(),
            'supervisor_rut' => $this->string(12)->null(),
            'supervisor_nombre' => $this->string(255)->null(),
            'supervisor_correo' => $this->string(255)->null(),
            'supervisor_telefono' => $this->string(20)->null(),
            'supervisor_cargo' => $this->string(255)->null(),
            'activo' => $this->integer()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null(),
        ]);

        $this->createIndex(
            'idx-empresa-rut',
            '{{%empresa}}',
            'rut'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%empresa}}');
    }
}
