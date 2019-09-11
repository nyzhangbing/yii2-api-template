<?php

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/../environments/' . YII_ENV . '/config/params-local.php')
);

$config = [
    'id' => 'basic-console',
    'timeZone' => 'Asia/Shanghai',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands\controllers',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mutex' => [
            'class' => 'yii\redis\Mutex',
            'redis' => 'redis',
            'expire' => 3600,
        ],
        'alarm' => [
            'class' => 'app\services\AuthorizationService\SSOAlarmService'
        ],
    ],
    'params' => $params,
];

return $config;
