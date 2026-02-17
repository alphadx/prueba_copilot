<?php

use yii\db\Migration;

/**
 * Seeds initial users for testing
 */
class m260217_000002_seed_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $users = [
            [
                'username' => 'admin',
                'password' => 'admin123',
                'nombre' => 'Administrador SGDII',
                'rut' => '11111111-1',
                'correo' => 'admin@sgdii.cl',
                'rol' => 'admin',
            ],
            [
                'username' => 'prof.martinez',
                'password' => 'prof123',
                'nombre' => 'Juan Martínez López',
                'rut' => '12345678-9',
                'correo' => 'jmartinez@usach.cl',
                'rol' => 'profesor',
            ],
            [
                'username' => 'prof.gonzalez',
                'password' => 'prof123',
                'nombre' => 'María González Soto',
                'rut' => '13456789-0',
                'correo' => 'mgonzalez@usach.cl',
                'rol' => 'profesor',
            ],
            [
                'username' => 'prof.rodriguez',
                'password' => 'prof123',
                'nombre' => 'Pedro Rodríguez Muñoz',
                'rut' => '14567890-1',
                'correo' => 'prodriguez@usach.cl',
                'rol' => 'profesor',
            ],
            [
                'username' => 'prof.silva',
                'password' => 'prof123',
                'nombre' => 'Ana Silva Vargas',
                'rut' => '15678901-2',
                'correo' => 'asilva@usach.cl',
                'rol' => 'comision_evaluadora',
            ],
            [
                'username' => 'prof.morales',
                'password' => 'prof123',
                'nombre' => 'Luis Morales Díaz',
                'rut' => '16789012-3',
                'correo' => 'lmorales@usach.cl',
                'rol' => 'comision_evaluadora',
            ],
            [
                'username' => 'alumno.perez',
                'password' => 'alumno123',
                'nombre' => 'Carlos Pérez Torres',
                'rut' => '20123456-7',
                'correo' => 'cperez@usach.cl',
                'rol' => 'alumno',
            ],
            [
                'username' => 'alumno.rojas',
                'password' => 'alumno123',
                'nombre' => 'José Rojas Fuentes',
                'rut' => '20234567-8',
                'correo' => 'jrojas@usach.cl',
                'rol' => 'alumno',
            ],
            [
                'username' => 'alumno.diaz',
                'password' => 'alumno123',
                'nombre' => 'Valentina Díaz Ramos',
                'rut' => '20345678-9',
                'correo' => 'vdiaz@usach.cl',
                'rol' => 'alumno',
            ],
            [
                'username' => 'alumno.lopez',
                'password' => 'alumno123',
                'nombre' => 'Francisca López Vera',
                'rut' => '20456789-0',
                'correo' => 'flopez@usach.cl',
                'rol' => 'alumno',
            ],
        ];

        foreach ($users as $userData) {
            // Generate password hash
            $passwordHash = Yii::$app->security->generatePasswordHash($userData['password']);
            $authKey = Yii::$app->security->generateRandomString();

            $this->insert('{{%user}}', [
                'username' => $userData['username'],
                'password_hash' => $passwordHash,
                'nombre' => $userData['nombre'],
                'rut' => $userData['rut'],
                'correo' => $userData['correo'],
                'rol' => $userData['rol'],
                'activo' => 1,
                'auth_key' => $authKey,
                'access_token' => Yii::$app->security->generateRandomString(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%user}}');
    }
}
