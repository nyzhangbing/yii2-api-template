<?php
/**
 * Created by PhpStorm.
 * User: zhangbing
 * Date: 2019/3/28
 * Time: 11:44
 */

namespace app\services\DataService\Account;

use app\core\QcException;
use app\enums\BaseStatusEnum;
use app\enums\UserStatusEnum;
use app\models\account\User;
use app\services\DataService\Account\ServiceInterface\IUserService;
use app\services\DataService\QcDataServiceBase;
use yii\base\Exception;

class UserService extends QcDataServiceBase implements IUserService
{

    protected function getModelClass()
    {
        return 'app\models\account\User';
    }

    public function getList(array $params, int $pageIndex, int $pageSize)
    {
        return User::find()
            ->with(['userPrincipals', 'userPrincipals.principal'])
            ->offset(($pageIndex - 1) * $pageSize)
            ->limit($pageSize)
            ->orderBy('id DESC')
            ->all();
    }

    public function add($userName, $mobile, $email, $password)
    {
        $model = new User();
        $model->user_name = $userName;
        $model->mobile = $mobile;
        $model->email = $email;
        $model->password = $password;
        $model->status = BaseStatusEnum::启用;
        if (!$model->save())
            throw new Exception(current($model->getFirstErrors()));
        return $model;
    }
}