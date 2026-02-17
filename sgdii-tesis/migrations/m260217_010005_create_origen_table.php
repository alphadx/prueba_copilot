<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%origen}}`.
 */
class m260217_010005_create_origen_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%origen}}', [
            'id' => $this->primaryKey(),
            'nombre' => $this->string(100)->notNull()->unique(),
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
        $this->dropTable('{{%origen}}');
    }
}
