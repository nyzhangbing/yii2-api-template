<?php

namespace app\controllers;

use app\constants\ErrInfo;
use app\core\{
    QcController, QcException, QcResponse
};
use app\models\LoginForm;
use yii\base\Exception;

class UserController extends QcController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = ['login'];
        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'login' => ['POST', 'OPTIONS'],
            'logout' => ['POST', 'OPTIONS'],
        ];
    }

    public function actionLogin()
    {
        $data = \Yii::$app->request->post();
        if (empty($data))
            throw new QcException(ErrInfo::MISS_REQUIRE_PARAMS);
        $model = new LoginForm;
        $model->setAttributes($data);
        $user = $model->login();
        if (!$user)
            throw new Exception(current($model->getFirstErrors()));
        $data = new \stdClass();
        $data->id = $user->id;
        $data->user_name = $user->user_name;
        $data->email = $user->email;
        $data->mobile = $user->mobile;
        $data->token = $user->token;
        return new QcResponse($data);
    }

    public function actionLogout()
    {
        $user = \Yii::$app->user->getIdentity();
        if ($user) {
            $user->removeAuthKey();
            $user->save();
        }
        return new QcResponse();
    }
}