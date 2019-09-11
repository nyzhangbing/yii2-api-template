<?php
/**
 * Created by PhpStorm.
 * User: zhangbing
 * Date: 2019/3/28
 * Time: 13:31
 */

namespace app\modules\account\businesses\BusinessInterface;


use app\core\GridDataSource;

interface IUserBusiness
{
    public function getList($params, $pageIndex, $pageSize): GridDataSource;

    public function enable($id);

    public function disable($id);

    public function add($userName, $mobile, $email, $password);

    public function update($id, $userName, $mobile, $email, $password);

    public function getModelById($id);
}