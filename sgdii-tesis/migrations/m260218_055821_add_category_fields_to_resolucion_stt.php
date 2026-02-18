<?php

use yii\db\Migration;

/**
 * Adds category and subcategory fields to resolucion_stt table for commission evaluation
 */
class m260218_055821_add_category_fields_to_resolucion_stt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%resolucion_stt}}', 'categoria_id', $this->integer()->null());
        $this->addColumn('{{%resolucion_stt}}', 'subcategoria_id', $this->integer()->null());
        
        // Create indexes for better query performance
        $this->createIndex('idx-resolucion_stt-categoria_id', '{{%resolucion_stt}}', 'categoria_id');
        $this->createIndex('idx-resolucion_stt-subcategoria_id', '{{%resolucion_stt}}', 'subcategoria_id');
        
        // Note: SQLite does not support adding foreign keys after table creation.
        // For production with MySQL/PostgreSQL, foreign key constraints should be added.
        // For this SQLite prototype, referential integrity is enforced at the application level
        // through model validation rules (see ResolucionStt::rules()).
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-resolucion_stt-subcategoria_id', '{{%resolucion_stt}}');
        $this->dropIndex('idx-resolucion_stt-categoria_id', '{{%resolucion_stt}}');
        
        $this->dropColumn('{{%resolucion_stt}}', 'subcategoria_id');
        $this->dropColumn('{{%resolucion_stt}}', 'categoria_id');
    }
}
