<?php
/**
 * Created by PhpStorm.
 * User: zhangbing
 * Date: 2019/3/28
 * Time: 13:30
 */

namespace app\modules\account\businesses;

use app\core\EnumTranslator;
use app\core\GridDataSource;
use app\enums\BaseStatusEnum;
use app\models\account\User;
use app\modules\account\businesses\BusinessInterface\IUserBusiness;
use app\services\DataService\Account\ServiceInterface\IUserService;
use yii\web\NotFoundHttpException;

class UserBusiness implements IUserBusiness
{
    private $userService;

    function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    public function getList($params, $pageIndex, $pageSize): GridDataSource
    {
        $condition = ['and'];
        if ($params['user_name'] !== '')
            array_push($condition, ['like', 'user_name', $params['user_name']]);
        if ($params['mobile'] !== '')
            array_push($condition, ['=', 'mobile', $params['mobile']]);
        if ($params['status'] !== '')
            array_push($condition, ['=', 'status', $params['status']]);
        $models = User::find()
            ->where($condition)
            ->offset(($pageIndex - 1) * $pageSize)
            ->limit($pageSize)
            ->orderBy('id DESC')
            ->all();
        $items = array_map(function ($e) {
            return [
                'id' => $e->id,
                'user_name' => $e->user_name,
                'mobile' => $e->mobile,
                'email' => $e->email,
                'status' => $e->status,
                'status_text' => EnumTranslator::Translate(BaseStatusEnum::className(), $e->status),
                'created_at' => $e->created_at,
                'updated_at' => $e->updated_at
            ];
        }, $models);
        $total = User::find()->where($condition)->count();
        return new GridDataSource($items, $total);
    }

    public function enable($id)
    {
        return $this->userService->updateByCondition(['id' => $id], [
            'status' => BaseStatusEnum::启用
        ]);
    }

    public function disable($id)
    {
        return $this->userService->updateByCondition(['id' => $id], [
            'status' => BaseStatusEnum::禁用
        ]);
    }

    public function add($userName, $mobile, $email, $password)
    {
        return $this->userService->add($userName, $mobile, $email, md5($password));
    }

    public function update($id, $userName, $mobile, $email, $password)
    {
        $model = $this->userService->findModel($id);
        if (!$model)
            throw new NotFoundHttpException();
        $attributes = [
            'user_name' => $userName,
            'mobile' => $mobile,
            'email' => $email,
            'updated_at' => time()
        ];
        if ($password !== 'password')
            $attributes['password'] = md5($password);
        $this->userService->updateByCondition(['id' => $id], $attributes);
    }

    public function getModelById($id)
    {
        $model = User::find()
            ->with(['userPrincipals', 'userPrincipals.principal'])
            ->where(['id' => $id])
            ->one();
        if (!$model)
            throw new NotFoundHttpException();
        $principals = [];
        if (isset($model['userPrincipals'])) {
            $principals = array_map(function ($userPrincipal) {
                if (isset($userPrincipal['principal'])) {
                    $obj = new \stdClass();
                    $obj->id = $userPrincipal->principal_id;
                    $obj->name = $userPrincipal->principal->name;
                    return $obj;
                }
                return null;
            }, $model->userPrincipals);
        }
        $user = new \stdClass();
        $user->id = $model->id;
        $user->user_name = $model->user_name;
        $user->mobile = $model->mobile;
        $user->email = $model->email;
        $user->principals = $principals;
        $user->status = $model->status;
        $user->status_text = EnumTranslator::Translate(BaseStatusEnum::className(), $user->status);
        return $user;
    }
}