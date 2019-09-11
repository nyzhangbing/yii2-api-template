<?php
/**
 * Created by PhpStorm.
 * User: zhangbing
 * Date: 2019/3/29
 * Time: 11:13
 */

namespace app\constants;


class ErrInfo
{
    const SYSTEM_ERROR = ['code' => 1000, 'message' => '系统错误，请稍后再试'];
    const MISS_REQUIRE_PARAMS = ['code' => 1001, 'message' => '必填参数缺失'];
    const INVALID_PARAMS = ['code' => 1002, 'message' => '无效的请求参数'];
    const USERNAME_PASSWORD_DOES_NOT_MATCH = ['code' => 1003, 'message' => '用户名或密码错误'];
    const ALREADY_EXIST = ['code' => 1004, 'message' => '资源已存在'];
    const REQUEST_RESOURCE_NOT_FOUND = ['code' => 1005, 'message' => '请求的资源不存在'];
    const ACTION_CALL_REPEAT = ['code' => 1006, 'message' => '重复调用'];
    const GET_LOCK_FAILED = ['code' => 1007, 'message' => '获取锁失败'];
}
