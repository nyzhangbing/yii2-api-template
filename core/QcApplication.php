<?php

namespace app\core;


use yii\web\Application;

class QcApplication extends Application
{
    public $request_id;

    public function init()
    {
        $this->request_id = \Yii::$app->security->generateRandomString();
        parent::init();
    }

    public function getRequestId()
    {
        return $this->request_id;
    }
}