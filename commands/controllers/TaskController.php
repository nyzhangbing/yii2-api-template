<?php
/**
 * Created by PhpStorm.
 * User: zhangbing
 * Date: 2019/7/29
 * Time: 14:06
 */

namespace app\commands\controllers;

use app\commands\tasks\Task;
use app\core\Common;
use yii\base\Exception;
use yii\console\Controller;

class TaskController extends Controller
{
    public function actionRun($className)
    {
        try {
            if (empty($className))
                throw new Exception('请指定Task类名');
            $className = base64_decode($className);
            $reflection = new \ReflectionClass($className);
            $instance = $reflection->newInstance();
            if (!($instance instanceof Task))
                throw new \Exception('Task必须是\'console\tasks\Task\'类型');
            $method = new \ReflectionMethod($className, 'runTask');
            $method->invoke($instance);
        } catch (\Exception $ex) {
            \Yii::error(Common::formatExceptionMessage($ex));
        }
    }
}