<?php

namespace app\core;

use yii\helpers\Inflector;

class UrlRule extends \yii\rest\UrlRule
{
    public $extraPatterns = [
        'POST {id}/{operation}' => 'handle',
        'PATCH {id}/{attr}' => 'set-attr',
        'GET {id}/{attr}' => 'get-attr',
        '{id}/\w+' => 'options',
    ];

    public $tokens = [
        '{id}' => '<id:\d+>',
        '{operation}' => '<operation:\w+>',
        '{attr}' => '<attr:\w+>',
    ];

    public function init()
    {
        $this->registerRouters();
        parent::init();
    }

    private function getControllerPathByNamespace($controllerNamespace)
    {
        return \Yii::getAlias('@' . str_replace('\\', '/', $controllerNamespace));
    }

    private function registerRouters()
    {
        $this->controller = [];
        $modules = \Yii::$app->modules;
        foreach ($modules as $moduleName => $module) {
            if (is_array($module) && isset($module['autoRegisterRouters']) && $module['autoRegisterRouters']) {
                if (!isset($module['controllerNamespace']))
                    continue;
                $controllerPath = $this->getControllerPathByNamespace($module['controllerNamespace']);
                if (is_dir($controllerPath)) {
                    $files = scandir($controllerPath);
                    foreach ($files as $file) {
                        if (!empty($file) && substr_compare($file, 'Controller.php', -14, 14) === 0) {
                            $controllerName = substr(basename($file), 0, -14);
                            $controllerFullName = sprintf('%s/%s', $moduleName, Inflector::camel2id($controllerName));
                            array_push($this->controller, $controllerFullName);
                        }
                    }
                }
            }
        }
    }
}