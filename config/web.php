<?php

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/../environments/' . YII_ENV . '/config/params-local.php')
);

$config = [
    'id' => 'yii2-api',
    'language' => 'zh-CN',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'Asia/Shanghai',
    'controllerNamespace' => 'app\controllers',
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => [
                    'class' => 'yii\web\JsonParser',
                    'asArray' => true
                ],
                'text/json' => [
                    'class' => 'yii\web\JsonParser',
                    'asArray' => true
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\account\User',
            'enableAutoLogin' => true,
            'enableSession' => false,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'cache' => YII_ENV_PROD ? 'cache' : false,
            'rules' => [
                '' => '/site/index',
                [
                    'class' => 'app\core\UrlRule',
                ],
                '/<module:\w+>/<controller:\w+>/<action:\w+>' => '/<module>/<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'fileMap' => [
                        'app' => 'app.php',
                        'enum' => 'enum.php',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'class' => 'app\core\ErrorHandler'
        ],
        'mutex' => [
            'class' => 'yii\redis\Mutex',
            'redis' => 'redis',
            'expire' => 120,
        ],
    ],
    'modules' => [
        'account' => [
            'class' => 'app\modules\account\Module',
            'businessNamespace' => 'app\modules\account\businesses',
            'controllerNamespace' => 'app\modules\account\controllers',
            'autoRegisterRouters' => true
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1']

    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];;
}

return $config;
