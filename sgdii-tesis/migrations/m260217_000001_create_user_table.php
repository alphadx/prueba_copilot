<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m260217_000001_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull()->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'nombre' => $this->string(255)->notNull(),
            'rut' => $this->string(12)->notNull()->unique(),
            'correo' => $this->string(255),
            'rol' => $this->string(50)->notNull(),
            'activo' => $this->boolean()->defaultValue(1),
            'auth_key' => $this->string(32),
            'access_token' => $this->string(32),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Create indexes
        $this->createIndex(
            'idx-user-username',
            '{{%user}}',
            'username'
        );

        $this->createIndex(
            'idx-user-rut',
            '{{%user}}',
            'rut'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
