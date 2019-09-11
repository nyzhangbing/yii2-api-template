<?php
/**
 * Created by PhpStorm.
 * User: zhangbing
 * Date: 2019/3/28
 * Time: 11:41
 */

namespace app\services\DataService\Account\ServiceInterface;

use app\services\DataService\QcDataServiceInterface;

interface IUserService extends QcDataServiceInterface
{
    public function getList(array $params, int $pageIndex, int $pageSize);

    public function add($userName, $mobile, $email, $password);
}