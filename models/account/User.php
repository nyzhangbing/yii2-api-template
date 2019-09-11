<?php

namespace app\models\account;

use app\enums\BaseStatusEnum;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "tb_user".
 *
 * @property string $id
 * @property string $user_name
 * @property string $mobile
 * @property string $email
 * @property string $password
 * @property integer $status
 * @property string $token
 * @property string $token_expire
 * @property string $created_at
 * @property string $updated_at
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_user';
    }


    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'status', 'token_expire', 'created_at', 'updated_at'], 'integer'],
            [['user_name'], 'string', 'max' => 10],
            [['email'], 'string', 'max' => 30],
            [['password'], 'string', 'max' => 32],
            [['token'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'user_name' => '用户名',
            'mobile' => '手机号',
            'email' => '邮箱',
            'password' => '密码',
            'status' => '状态',
            'token' => 'token',
            'token_expire' => 'token过期时间',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (!$token)
            return null;
        return static::findOne(['token' => $token, 'status' => BaseStatusEnum::启用]);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->token;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->token == $authKey && $this->token_expire > time();
    }

    public function generateAuthKey()
    {
        $this->token = Yii::$app->security->generateRandomString();
        $this->token_expire = time() + Yii::$app->params['token_duration'];
    }

    public function removeAuthKey()
    {
        $this->token = '';
        $this->token_expire = 0;
    }

    public function validatePassword($password)
    {
        return $this->password == md5($password);
    }

    public function validateStatus()
    {
        return $this->status == BaseStatusEnum::启用;
    }

    public static function findByMobile($mobile)
    {
        return static::findOne(['mobile' => $mobile]);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }
}
