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
####创建一个restful风格的控制器

1、创建一个模块
~~~
      businesses/                   业务代码实现
      businesses/BusinessInterface  为业务定义规范接口
      controllers/                  模块对应的控制器
~~~
目录结构请按照此标准，接口和实现类命名需满足一定标准 如用户服务`UserBusiness`对应的接口应该是`BusinessInterface/IUserBusiness`,遵循此标准对应的接口和实现类的依赖关系将会自动注册

2、创建一个控制器

```php
<?php

namespace app\modules\account\controllers;

use app\constants\ErrInfo;
use app\core\{
    QcController, QcException, QcResponse
};
use app\modules\account\businesses\BusinessInterface\IUserBusiness;
use yii\base\NotSupportedException;

class UserController extends QcController
{
    private $userBusiness;

    function __construct($id, $module, IUserBusiness $userBusiness, array $config = [])
    {
        $this->userBusiness = $userBusiness;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $pageIndex = \Yii::$app->request->get('pageIndex', 1);
        $pageSize = \Yii::$app->request->get('pageSize', 10);
        $userName = \Yii::$app->request->get('user_name', '');
        $mobile = \Yii::$app->request->get('mobile', '');
        $status = \Yii::$app->request->get('status', '');
        $data = $this->userBusiness->getList([
            'user_name' => $userName,
            'mobile' => $mobile,
            'status' => $status
        ], $pageIndex, $pageSize);
        return new QcResponse($data);
    }

    public function actionHandle($id, $operation)
    {
        switch ($operation) {
            case 'enable':
                $this->userBusiness->enable($id);
                break;
            case 'disable':
                $this->userBusiness->disable($id);
                break;
            case 'setprincipals':
                $principals = \Yii::$app->request->post('principals', []);
                $this->userBusiness->setPrincipals($id, $principals);
                break;
            default:
                throw new NotSupportedException('不支持的操作');
        }
        return new QcResponse();
    }

    public function actionCreate()
    {
        $data = \Yii::$app->request->post();
        if (empty($data))
            throw new QcException(ErrInfo::MISS_REQUIRE_PARAMS);
        $this->userBusiness->add($data['user_name'], $data['mobile'], $data['email'], $data['password']);
        return new QcResponse();
    }

    public function actionUpdate($id)
    {
        $data = \Yii::$app->request->post();
        if (empty($data))
            throw new QcException(ErrInfo::MISS_REQUIRE_PARAMS);
        $this->userBusiness->update($id, $data['user_name'], $data['mobile'], $data['email'], $data['password']);
        return new QcResponse();
    }

    public function actionView($id)
    {
        $data = $this->userBusiness->getModelById($id);
        return new QcResponse($data);
    }
}
```
此处`actionHandle`是框架基于yii2原生restful扩展出的方法，用于实现HttpMethod不能表达的对于资源的操作行为，如启用、禁用等操作

3、配置模块
在`config/web.php`中`modules`节点下，配置启用该模块
```php
'modules' => [
        'account' => [
            'class' => 'app\modules\account\Module',
            'businessNamespace' => 'app\modules\account\businesses',
            'controllerNamespace' => 'app\modules\account\controllers',
            'autoRegisterRouters' => true
        ],
    ],
```
调用示例：

| 获取用户列表     | GET    | http://host:port/account/users           |
| ---------------- | ------ | ---------------------------------------- |
| 创建一个用户     | POST   | http://host:port/account/users           |
| 获取单个用户信息 | GET    | http://host:port/account/users/1         |
| 更新用户信息     | PUT    | http://host:port/account/users/1         |
| 删除一个用户     | DELETE | http://host:port/account/users/1         |
| 禁用一个用户     | POST   | http://host:port/account/users/1/disable |


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