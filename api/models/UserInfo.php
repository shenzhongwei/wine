<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "user_info".
 *
 * @property integer $id
 * @property string $phone
 * @property string $sex
 * @property string $head_url
 * @property string $birth
 * @property string $nickname
 * @property string $realname
 * @property string $invite_user_id
 * @property string $invite_code
 * @property integer $is_vip
 * @property integer $status
 * @property string $created_time
 * @property string $updated_time
 * @property UserAddress[] $userAddresses
 * @property UserLogin $userLogins
 */
class UserInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sex','invite_code'], 'string'],
            [['is_vip', 'status','invite_user_id'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['phone'], 'string', 'max' => 13],
            [['head_url'], 'string', 'max' => 128],
            [['birth'], 'string', 'max' => 255],
            [['nickname', 'realname','invite_code'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'phone' => '注册手机号',
            'sex' => '性别',
            'head_url' => '头像地址',
            'birth' => '生日',
            'nickname' => '昵称',
            'realname' => '真实姓名',
            'invite_user_id' => '邀请人id',
            'invite_code'=>'用户邀请码',
            'is_vip' => '是否为会员 0不是 1是',
            'status' => '状态 0删除 1正常',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLogin()
    {
        return $this->hasOne(UserLogin::className(), ['uid' => 'id', 'status' => 'status']);
    }

    public function getUserAddresses()
    {
        return $this->hasMany(UserAddress::className(), ['uid' => 'id']);
    }

    public static function getInfoByInviteCode($inviteCode){
        return static::findOne(['invite_code'=>$inviteCode]);
    }

    public static function GenerateCode($user_id){
        $arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','2','3','4','5','6','7','8','9',($user_id%10)];
        $code = '';
        for($i=1;$i<=8;$i++){
            $key = array_rand($arr);
            $code.=$arr[$key];
        }
        return $code;
    }
}
