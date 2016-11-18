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
 * @property integer $invite_user_id
 * @property integer $is_vip
 * @property string $invite_code
 * @property integer $status
 * @property string $created_time
 * @property string $updated_time
 *
 * @property GoodCollection[] $goodCollections
 * @property UserPayPassword $userPayPassword
 * @property OrderComment[] $orderComments
 * @property OrderInfo[] $orderInfos
 * @property ShoppingCert[] $shoppingCerts
 * @property UserAddress[] $userAddresses
 * @property UserLogin $userLogin
 * @property UserPromotion[] $userPromotions
 * @property UserTicket[] $userTickets
 * @property TicketInout[] $ticketInouts
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
            [['sex'], 'string'],
            [['invite_user_id', 'is_vip', 'status'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['phone'], 'string', 'max' => 13],
            [['head_url'], 'string', 'max' => 128],
            [['birth'], 'string', 'max' => 255],
            [['nickname', 'realname', 'invite_code'], 'string', 'max' => 32],
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
            'is_vip' => '是否为会员 0不是 1是',
            'invite_code' => '邀请码(不可更改)',
            'status' => '状态 0删除 1正常',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodCollections()
    {
        return $this->hasMany(GoodCollection::className(), ['uid' => 'id'])->where(['order_collection.status'=>1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderComments()
    {
        return $this->hasMany(OrderComment::className(), ['uid' => 'id'])->where(['order_comment.status'=>1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayPassword()
    {
        return $this->hasOne(UserPayPassword::className(), ['uid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfos()
    {
        return $this->hasMany(OrderInfo::className(), ['uid' => 'id'])->where(['order_info.is_del'=>0]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShoppingCerts()
    {
        return $this->hasMany(ShoppingCert::className(), ['uid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAddresses()
    {
        return $this->hasMany(UserAddress::className(), ['uid' => 'id'])->where(['user_address.status'=>1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLogin()
    {
        return $this->hasOne(UserLogin::className(), ['uid' => 'id', 'status' => 'status']);
    }

    public function getTicketInouts()
    {
        return $this->hasMany(TicketInout::className(), ['uid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPromotions()
    {
        return $this->hasMany(UserPromotion::className(), ['uid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTickets()
    {
        return $this->hasMany(UserTicket::className(), ['uid' => 'id']);
    }

    public static function getInfoByPhone($invitePhone){
        return static::findOne(['phone'=>$invitePhone]);
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
