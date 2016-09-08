<?php

namespace admin\models;

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
 * @property OrderComment[] $orderComments
 * @property OrderInfo[] $orderInfos
 * @property ShoppingCert[] $shoppingCerts
 * @property UserAddress[] $userAddresses
 * @property UserLogin[] $userLogins
 * @property UserPayPassword[] $userPayPasswords
 * @property UserPromotion[] $userPromotions
 * @property UserTicket[] $userTickets
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
            'id' => 'ID',
            'phone' => 'Phone',
            'sex' => 'Sex',
            'head_url' => 'Head Url',
            'birth' => 'Birth',
            'nickname' => 'Nickname',
            'realname' => 'Realname',
            'invite_user_id' => 'Invite User ID',
            'is_vip' => 'Is Vip',
            'invite_code' => 'Invite Code',
            'status' => 'Status',
            'created_time' => 'Created Time',
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
