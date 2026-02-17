<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%categoria}}`.
 */
class m260217_010007_create_categoria_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%categoria}}', [
            'id' => $this->primaryKey(),
            'nombre' => $this->string(255)->notNull()->unique(),
            'descripcion' => $this->text()->null(),
            'activo' => $this->integer()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%categoria}}');
    }
}
