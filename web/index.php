<?php

// comment out the following two lines when deployed to production
$env = get_cfg_var('env');
$env = $env ? $env : 'dev';
defined('YII_ENV') or define('YII_ENV', $env);
defined('YII_DEBUG') or define('YII_DEBUG', $env !== 'prod');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../core/QcApplication.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . "/../config/web.php"),
    require(__DIR__ . '/../environments/' . YII_ENV . '/config/web-local.php'),
    require(__DIR__ . '/../environments/' . YII_ENV . '/config/common-local.php'),
    require(__DIR__ . '/../environments/' . YII_ENV . '/config/db.php')
);

(new \app\core\QcApplication($config))->run();