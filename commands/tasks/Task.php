<?php

namespace app\commands\tasks;

use app\core\Common;
use yii\base\InvalidConfigException;
use yii\mutex\FileMutex;

abstract class Task extends \yii\base\Object
{
    /**
     * The mutex implementation.
     *
     * @var \yii\mutex\Mutex
     */
    private $_mutex;

    function init()
    {
        $this->_mutex = \Yii::$app->has('mutex') ? \Yii::$app->get('mutex') : (new FileMutex());
        parent::init();
    }

    /**
     * 任务名称
     * @return string
     */
    abstract protected function getTaskName(): string;

    /**
     * 任务处理器
     * @param array $params
     * @return mixed
     */
    abstract protected function run(array $params);

    /**
     * 是否只能运行单个进程
     * @return bool
     */
    protected function runOnOneServer()
    {
        return true;
    }

    /**
     * 是否只在生产环境运行
     * @return bool
     */
    protected function runOnlyProd()
    {
        return false;
    }

    private function mutexName()
    {
        return 'framework/task-' . sha1($this->getTaskName());
    }

    public function runTask(array $params = [])
    {
        try {
            if (!YII_ENV_PROD && $this->runOnlyProd())
                return;
            if ($this->runOnOneServer()) {
                if ($this->_mutex instanceof FileMutex)
                    throw new InvalidConfigException('You must config mutex in the application component, except the FileMutex.');
                if (!$this->_mutex->acquire($this->mutexName()))
                    return;
            }
            $this->logInfo('task is running...');
            $this->run($params);
            $this->logInfo('task is completed');
        } catch (\Exception $ex) {
            $this->logError(Common::formatExceptionMessage($ex));
        } finally {
            if ($this->runOnOneServer())
                $this->_mutex->release($this->mutexName());
        }
    }

    /**
     * 输出脚本日志
     * @param string $message
     */
    final protected function logInfo($message)
    {
        $formattedMessage = $this->formatMessage($message);
        \Yii::info($formattedMessage);
    }

    /**
     * 输出错误日志
     * @param string $message
     */
    final protected function logError($message)
    {
        $formattedMessage = $this->formatMessage($message);
        \Yii::error($formattedMessage);
    }

    /**
     * 格式化日志
     * @param $message
     * @return string
     */
    private function formatMessage($message)
    {
        $pid = function_exists('posix_getpid') ? posix_getpid() : '';
        $date = date('Y-m-d H:i:s');
        $mem = floor(memory_get_usage(true) / 1024 / 1024) . 'MB';
        $formattedMessage = sprintf('%s:[%s pid:%s memory:%s]%s', $this->getTaskName(), $date, $pid, $mem, $message);
        return $formattedMessage;
    }
}