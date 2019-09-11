<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            // 配置主服务器
            'dsn' => 'mysql:host=127.0.0.1;dbname=yii2;port=3306',
            'username' => 'root',
            'password' => '123',
            'tablePrefix' => 'tb_',
            'charset' => 'utf8mb4',
            'enableSlaves' => false,
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 10,
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ],
    ]
];
