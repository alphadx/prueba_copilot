<?php

namespace app\commands;

use yii\console\Controller;
use app\models\User;

/**
 * Shell command for user management
 */
class ShellController extends Controller
{
    /**
     * Check if a user exists
     * @param string $username
     */
    public function actionCheckUser($username)
    {
        $user = User::findByUsername($username);
        if ($user) {
            echo "found\n";
        } else {
            echo "not_found\n";
        }
    }

    /**
     * Create admin user
     */
    public function actionCreateAdmin()
    {
        $user = new User();
        $user->username = 'admin';
        $user->nombre = 'Administrador';
        $user->setPassword('admin123');
        $user->generateAuthKey();
        
        if ($user->save()) {
            echo "Usuario administrador creado exitosamente.\n";
        } else {
            echo "Error al crear usuario administrador.\n";
            print_r($user->errors);
        }
    }
}
