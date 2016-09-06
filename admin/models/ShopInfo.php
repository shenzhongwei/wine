<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "shop_info".
 *
 * @property integer $id
 * @property string $name
 * @property integer $wa_id
 * @property integer $merchant
 * @property string $region
 * @property string $address
 * @property integer $lat
 * @property integer $lng
 * @property integer $limit
 * @property string $least_money
 * @property string $send_bill
 * @property string $no_send_need
 * @property string $bus_pic
 * @property string $logo
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $active_at
 * @property string $province
 * @property string $city
 * @property string $district
 *
 * @property OrderInfo[] $orderInfos
 * @property WineAdmin $wa
 * @property MerchantInfo $merchant0
 */
class ShopInfo extends \yii\db\ActiveRecord
{
    public $wa_username;
    public $wa_password;
    public $wa_type;
    public $wa_logo;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wa_id', 'merchant', 'lat', 'lng', 'limit', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['least_money', 'send_bill', 'no_send_need'], 'number'],
            [['name', 'region'], 'string', 'max' => 50],
            [['address', 'bus_pic', 'logo', 'province', 'city', 'district'], 'string', 'max' => 128],
            [['wa_id'], 'exist', 'skipOnError' => true, 'targetClass' =>Admin::className(), 'targetAttribute' => ['wa_id' => 'wa_id']],
            [['merchant'], 'exist', 'skipOnError' => true, 'targetClass' => MerchantInfo::className(), 'targetAttribute' => ['merchant' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '门店名称',
            'wa_id' => '后台管理员id',
            'merchant' => '所属商户',
            'region' => '所在地区',
            'address' => '详细地址',
            'lat' => '纬度',
            'lng' => '经度',
            'limit' => '配送范围',
            'least_money' => '订单最低金额',
            'send_bill' => '配送金额',
            'no_send_need' => '免配送条件',
            'bus_pic' => '营业执照',
            'logo' => '门店logo',
            'regist_at' => '入驻时间',
            'is_active' => '是否激活',
            'active_at' => '激活状态更改时间',
            'province' => '省',
            'city' => '市',
            'district' => '区',

            'wa_username'=>'后台登陆名',
            'wa_password'=>'后台登陆密码',
            'wa_type'=>'用户组类型',
            'wa_logo'=>'用户头像'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfos()
    {
        return $this->hasMany(OrderInfo::className(), ['sid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWa()
    {
        return $this->hasOne(Admin::className(), ['wa_id' => 'wa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerchant0()
    {
        return $this->hasOne(MerchantInfo::className(), ['id' => 'merchant']);
    }
}