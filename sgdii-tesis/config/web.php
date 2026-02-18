<?php

require __DIR__ . '/env.php';

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'sgdii-tesis',
    'name' => env('APP_NAME', 'SGDII - Módulo Tesis'),
    'language' => env('APP_LANGUAGE', 'es'),
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/yidas/yii2-bower-asset/bower',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => env('COOKIE_VALIDATION_KEY', 'sgdii-tesis-secret-key-change-in-production'),
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '' => 'site/index',
                'login' => 'site/login',
                'logout' => 'site/logout',
                
                // STT routes
                'stt/create' => 'stt/create',
                'stt/view/<id:\d+>' => 'stt/view',
                'stt/<action>' => 'stt/<action>',
                
                // Comision routes
                'comision' => 'comision/index',
                'comision/review/<id:\d+>' => 'comision/review',
                'comision/<action>' => 'comision/<action>',
                
                // Report routes
                'reports' => 'report/index',
                'report/<action>' => 'report/<action>',
                
                // Notification routes
                'notifications' => 'notification/index',
                'notification/mark-read/<id:\d+>' => 'notification/mark-read',
                'notification/<action>' => 'notification/<action>',
                
                // Default routes
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default for development
            'useFileTransport' => env('MAIL_USE_FILE_TRANSPORT', true),
            'fileTransportPath' => '@runtime/mail',
            // Configure SMTP settings for production
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => env('MAIL_HOST', 'localhost'),
                'username' => env('MAIL_USERNAME', ''),
                'password' => env('MAIL_PASSWORD', ''),
                'port' => env('MAIL_PORT', 587),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            ],
        ],
        'notificationService' => [
            'class' => 'app\components\NotificationService',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
