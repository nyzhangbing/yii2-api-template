<?php

namespace app\core;

use app\models\account\User;
use yii\rest\ActiveController;

class QcController extends ActiveController
{
    public $modelClass = '';

    /**
     * 请求对象
     * @var \yii\web\Request
     */
    protected $request;

    public function init()
    {
        parent::init();
        $this->request = \Yii::$app->request;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        return $actions;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => YII_ENV_PROD
                    ? [
                        //此处设置允许跨域的域名
                    ]
                    : ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => false,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => ['Content-Disposition'],
            ],
        ];
        $behaviors['logger'] = [
            'class' => HttpLogger::className()
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className()
        ];
        return $behaviors;
    }

    /**
     * @return null|User
     */
    public function getUser()
    {
        return \Yii::$app->getUser()->getIdentity();
    }
}