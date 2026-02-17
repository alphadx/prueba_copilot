<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%carrera_malla}}`.
 */
class m260217_010001_create_carrera_malla_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%carrera_malla}}', [
            'id' => $this->primaryKey(),
            'codigo' => $this->string(20)->notNull()->unique(),
            'nombre' => $this->string(255)->notNull(),
            'grado' => $this->string(50)->notNull(),
            'activo' => $this->integer()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null(),
        ]);

        $this->createIndex(
            'idx-carrera_malla-codigo',
            '{{%carrera_malla}}',
            'codigo'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%carrera_malla}}');
    }
}
