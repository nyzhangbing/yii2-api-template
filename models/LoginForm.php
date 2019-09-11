<?php

namespace app\models;


use app\models\account\User;
use yii\base\Model;

class LoginForm extends Model
{
    public $mobile;
    public $email;
    public $password;
    public $sms_code;
    public $login_type;
    private $_user;

    public function rules()
    {
        return [
            [['login_type'], 'required'],
            [['email', 'password'], 'required', 'when' => function () {
                return $this->login_type == 'email';
            }],
            ['password', 'validatePassword'],
            [['mobile', 'sms_code'], 'required', 'when' => function () {
                return $this->login_type == 'mobile';
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'mobile' => '手机号',
            'email' => '邮箱',
            'password' => '密码',
            'sms_code' => '短信验证码',
            'login_type' => '登录类型'
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validateStatus() || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码错误');
            }
        }
    }

    public function getUser()
    {
        if (!$this->_user) {
            if ($this->login_type === 'mobile') {
                $this->_user = User::findByMobile($this->mobile);
            } else {
                $this->_user = User::findByEmail($this->email);
            }
        }

        return $this->_user;
    }

    public function login()
    {
        if ($this->validate()) {
            $this->_user->generateAuthKey();
            $this->_user->save();
            return $this->_user;
        } else {
            return null;
        }
    }
}