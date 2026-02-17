<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%modalidad}}`.
 */
class m260217_010006_create_modalidad_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%modalidad}}', [
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
        $this->dropTable('{{%modalidad}}');
    }
}
