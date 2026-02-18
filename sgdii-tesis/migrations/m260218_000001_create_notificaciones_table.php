<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notificaciones}}`.
 */
class m260218_000001_create_notificaciones_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notificaciones}}', [
            'id' => $this->primaryKey(),
            'tipo' => $this->string(100)->notNull(),
            'contenido' => $this->text()->notNull(),
            'estado' => $this->string(20)->notNull()->defaultValue('No leída'),
            'usuario_destinatario_id' => $this->integer()->notNull(),
            'stt_id' => $this->integer()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null(),
        ]);

        // Create indexes
        $this->createIndex(
            'idx-notificaciones-usuario_destinatario_id',
            '{{%notificaciones}}',
            'usuario_destinatario_id'
        );

        $this->createIndex(
            'idx-notificaciones-estado',
            '{{%notificaciones}}',
            'estado'
        );

        $this->createIndex(
            'idx-notificaciones-stt_id',
            '{{%notificaciones}}',
            'stt_id'
        );

        // Add foreign key for usuario_destinatario_id (commented for SQLite compatibility)
        // $this->addForeignKey(
        //     'fk-notificaciones-usuario_destinatario_id',
        //     '{{%notificaciones}}',
        //     'usuario_destinatario_id',
        //     '{{%user}}',
        //     'id',
        //     'CASCADE'
        // );

        // Add foreign key for stt_id (commented for SQLite compatibility)
        // $this->addForeignKey(
        //     'fk-notificaciones-stt_id',
        //     '{{%notificaciones}}',
        //     'stt_id',
        //     '{{%solicitud_tema_tesis}}',
        //     'id',
        //     'SET NULL'
        // );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%notificaciones}}');
    }
}
