<?php
/**
 * Created by PhpStorm.
 * User: zhangbing
 * Date: 2019/6/27
 * Time: 17:00
 */

$config = [
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'app\core\LogstashTarget',
                    'host' => '127.0.0.1',
                    'port' => '5059',
                    'logVars' => [],
                    'categories' => ['application', 'curl'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'logVars' => [],
                    'categories' => ['application', 'curl'],
                ]
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.qq.com',
                'port' => '994',
                'username' => 'demo@qq.com',
                'password' => 'password',
                'encryption' => 'ssl',
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['demo@qq.com' => 'yii2']
            ],
        ]
    ],
];

return $config;