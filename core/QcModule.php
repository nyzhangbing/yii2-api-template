<?php

namespace app\core;

use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\caching\Cache;

class QcModule extends Module
{
    public $autoRegisterRouters = false;
    public $businessNamespace = '';
    public $dataServiceNamespace = 'app\services\DataService';
    private $cache = 'cache';
    protected $cacheKey = __CLASS__;

    public function init()
    {
        parent::init();
        if ($this->businessNamespace === null || empty($this->businessNamespace))
            throw new InvalidConfigException('The "businessNamespace" property must be set.');
        if (is_string($this->cache))
            $this->cache = \Yii::$app->get($this->cache, false);
        $dependencies = [];
        if (YII_ENV_PROD && $this->cache instanceof Cache) {
            $cacheKey = $this->cacheKey . '_' . $this->id;
            if (($data = $this->cache->get($cacheKey)) !== false) {
                $dependencies = $data;
            } else {
                $dataServiceDependencies = $this->buildDataServiceDependencies();
                $businessDependencies = $this->buildBusinessDependencies();
                $dependencies = array_merge($dataServiceDependencies, $businessDependencies);
                $this->cache->set($cacheKey, $dependencies, YII_ENV_PROD ? 1800 : 1);
            }
        } else {
            $dataServiceDependencies = $this->buildDataServiceDependencies();
            $businessDependencies = $this->buildBusinessDependencies();
            $dependencies = array_merge($dataServiceDependencies, $businessDependencies);
        }
        foreach ($dependencies as $interface => $class) {
            \Yii::$container->set($interface, $class);
        }
    }

    private function buildDataServiceDependencies()
    {
        $dependencies = [];
        $servicePath = $this->getDataServicePath();
        if (is_dir($servicePath)) {
            $dirs = scandir($servicePath);
            foreach ($dirs as $dir) {
                if ($dir === '.' || $dir === '..')
                    continue;
                $realPath = $servicePath . '/' . $dir;
                if (!is_dir($realPath))
                    continue;
                $files = scandir($realPath);
                foreach ($files as $file) {
                    if (!empty($file) && substr_compare($file, 'Service.php', -11, 11) === 0) {
                        $class = $this->dataServiceNamespace . '\\' . $dir . '\\' . substr(basename($file), 0, -4);
                        $interface = $this->dataServiceNamespace . '\\' . $dir . '\\' . 'ServiceInterface\\'
                            . 'I' . substr(basename($file), 0, -4);
                        $dependencies[$interface] = $class;
                    }
                }
            }
        }
        return $dependencies;
    }

    private function buildBusinessDependencies()
    {
        $dependencies = [];
        $businessPath = $this->getBusinessPath();
        if (is_dir($businessPath)) {
            $files = scandir($businessPath);
            foreach ($files as $file) {
                if (!empty($file) && substr_compare($file, 'Business.php', -12, 12) === 0) {
                    $class = $this->businessNamespace . '\\' . substr(basename($file), 0, -4);
                    $interface = $this->businessNamespace . '\\' . 'BusinessInterface\\'
                        . 'I' . substr(basename($file), 0, -4);
                    $dependencies[$interface] = $class;
                }
            }
        }
        return $dependencies;
    }

    private function getDataServicePath()
    {
        return \Yii::getAlias('@' . str_replace('\\', '/', $this->dataServiceNamespace));
    }

    private function getBusinessPath()
    {
        return \Yii::getAlias('@' . str_replace('\\', '/', $this->businessNamespace));
    }
}