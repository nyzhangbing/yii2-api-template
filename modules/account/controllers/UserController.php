<?php
/**
 * Created by PhpStorm.
 * User: zhangbing
 * Date: 2019/3/28
 * Time: 10:23
 */

namespace app\modules\account\controllers;


use app\constants\ErrInfo;
use app\core\QcController;
use app\core\QcException;
use app\core\QcResponse;
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