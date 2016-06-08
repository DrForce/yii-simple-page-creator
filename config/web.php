<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'name' => 'Test',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'en_GB',
    'charset' => 'UTF-8',
    'layout' => 'site',
    'timeZone' => 'Asia/Omsk',
    'defaultRoute' => 'site/index',
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'baseUrl' => '/',
            'rules' => [
                'pages/add' => 'site/new-page',
                'pages/edit/<id:\d+>' => 'site/edit-page',
                'pages/<page:\w+>' => 'site/index',
                'pages' => 'site/index'
            ]
        ],
        'formatter' => [
            'dateFormat' => 'd.MM.Y',
            'timeFormat' => 'H:mm:ss',
            'datetimeFormat' => 'd.MM.Y H:mm',
        ],
        'request' => [
            'baseUrl' => '',
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '123456',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'useFileTransport' => false,//set this property to false to send mails to real email addresses
            //comment the following array to send mail using php's mail function
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'testvkauditor@gmail.com',
                'password' => '123qweaA',
                'port' => '587',
                'encryption' => 'tls',
            ],
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
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', $_SERVER['REMOTE_ADDR']]
    ];
}

return $config;
