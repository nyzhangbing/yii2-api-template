基于yii2的restful api模板项目
============================

这是一个基于yii2的restful api模板项目，提供登录态管理、日志、性能分析、依赖的自动发现注册、对原生restful url rule进行扩展、任务计划管理等功能

目录结构
-------------------

      commands/           命令行程序
      config/             应用配置文件及系统任务配置
      constants/          常量
      core/               基础公共类
      enums/              枚举
      environments/       提供各个环境的配置文件
      modules/            模块
      services/           服务类
      controllers/        包含控制器
      mail/               邮件模板
      models/             数据模型
      web/                包含入口脚本及静态资源



要求
------------

此项目最低要求PHP 7.1


安装方式
------------
~~~
composer install
~~~

配置方式
-------------

####添加一个定时任务

1、创建任务类

```php
<?php

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
```

2、配置任务执行方式，在`config/schedule.php`中进行如下配置

```php
<?php
/** * @var \omnilight\scheduling\Schedule $schedule */

//每分钟执行一次
$schedule->command('task/run ' . base64_encode(\app\commands\tasks\DemoTask::class))
    ->everyMinute();
```
3、在crontab中进行配置

~~~
* * * * * php /path/to/yii yii schedule/run --scheduleFile=@app/config/schedule.php 1>> /dev/null 2>&1
~~~

计划任务更多用法请参考[omnilight/yii2-scheduling](https://github.com/omnilight/yii2-scheduling)