<?php
/**
 * Created by PhpStorm.
 * User: zhangbing
 * Date: 2019/9/11
 * Time: 10:51
 */

namespace app\commands\tasks;


class DemoTask extends Task
{

    /**
     * 任务名称
     * @return string
     */
    protected function getTaskName(): string
    {
        return '这是一个任务';
    }

    /**
     * 任务处理器
     * @param array $params
     * @return mixed
     */
    protected function run(array $params)
    {
        //do something...
    }
}