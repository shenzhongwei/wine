<?php

namespace api\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user_login".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $username
 * @property string $password
 * @property string $token
 * @property string $last_login_time
 * @property string $reg_id
 * @property integer $reg_type
 * @property integer $status
 *
 * @property UserInfo $u
 */
class UserLogin extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_login';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'reg_type', 'status'], 'integer'],
            [['token'], 'required'],
            [['last_login_time'], 'safe'],
            [['username', 'password'], 'string', 'max' => 50],
            [['token'], 'string', 'max' => 100],
            [['reg_id'], 'string', 'max' => 32],
            [['uid', 'status'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['uid' => 'id', 'status' => 'status']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'uid' => '所属用户id',
            'username' => '登录帐号',
            'password' => '登录密码',
            'token' => 'token(每次登录都会改变)',
            'last_login_time' => '最后登录时间',
            'reg_id' => '设备id',
            'reg_type' => '推送类型 1个人 2企业',
            'status' => '状态 1正常 0锁定',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid', 'status' => 'status']);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public static function findIdentityByUsername($username){
        return static::findOne(['username' => $username]);
    }


    public function getId()
    {
        return $this->uid;
    }

    public function getAuthKey()
    {
        return $this->token;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }


}
