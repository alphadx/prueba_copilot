<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subcategoria}}`.
 */
class m260217_010008_create_subcategoria_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subcategoria}}', [
            'id' => $this->primaryKey(),
            'categoria_id' => $this->integer()->notNull(),
            'nombre' => $this->string(255)->notNull(),
            'descripcion' => $this->text()->null(),
            'activo' => $this->integer()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-subcategoria-categoria_id',
            '{{%subcategoria}}',
            'categoria_id'
        );

        // Note: SQLite does not support adding foreign keys after table creation
        // Foreign keys should be defined in the table schema if needed
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subcategoria}}');
    }
}
