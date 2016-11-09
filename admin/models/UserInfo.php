<?php

namespace admin\models;

use common\fixtures\User;
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
 * @property UserAccount $a
 * @property GoodCollection[] $goodCollections
 * @property OrderComment[] $orderComments
 * @property OrderInfo[] $orderInfos
 * @property ShoppingCert[] $shoppingCerts
 * @property UserAddress[] $userAddresses
 * @property UserLogin[] $userLogins
 * @property UserPayPassword[] $userPayPasswords
 * @property UserPromotion[] $userPromotions
 * @property UserTicket[] $userTickets
 * @property TicketInout[] $ticketInouts
 */
class UserInfo extends \yii\db\ActiveRecord
{
    public $end;
    public $name;
    public $invite_user;
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
            [['created_time', 'updated_time','end'], 'safe'],
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
            'id' => 'ID',
            'phone' => '联系方式',
            'sex' => '性别',
            'head_url' => '头像',
            'birth' => '出生日期',
            'nickname' => '昵称',
            'realname' => '真实名称',
            'end'=>'账户余额',
            'invite_user_id' => '邀请人id',
            'is_vip' => '是否是会员',
            'invite_code' => '邀请码',
            'status' => '用户状态',
            'created_time' => '注册日期',
            'updated_time' => 'Updated Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodCollections()
    {
        return $this->hasMany(GoodCollection::className(), ['uid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderComments()
    {
        return $this->hasMany(OrderComment::className(), ['uid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfos()
    {
        return $this->hasMany(OrderInfo::className(), ['uid' => 'id']);
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
        return $this->hasMany(UserAddress::className(), ['uid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLogins()
    {
        return $this->hasMany(UserLogin::className(), ['uid' => 'id', 'status' => 'status']);
    }

    public function getTicketInouts()
    {
        return $this->hasMany(TicketInout::className(), ['uid' => 'id']);
    }


    public function getA(){
        return $this->hasOne(UserAccount::className(), ['target' => 'id'])->where("user_account.type=1 and user_account.level=2 and user_account.is_active=1");
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPayPasswords()
    {
        return $this->hasMany(UserPayPassword::className(), ['uid' => 'id']);
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


}
