<?php

namespace app\core;

use yii\mutex\Mutex;

class App
{

    /**
     * 获取进程锁
     * @return Mutex
     */
    public static function getLockComponent()
    {
        return \Yii::$app->mutex;
    }
}