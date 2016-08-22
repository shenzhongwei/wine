<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "shop_info".
 *
 * @property integer $id
 * @property string $name
 * @property integer $wa_id
 * @property integer $merchant
 * @property string $province
 * @property string $city
 * @property string $district
 * @property string $region
 * @property string $address
 * @property integer $lat
 * @property integer $lng
 * @property integer $limit
 * @property string $send_bill
 * @property string $bus_pic
 * @property string $logo
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $active_at
 *
// * @property WineAdmin $wa
 * @property MerchantInfo $merchant0
 */
class ShopInfo extends \yii\db\ActiveRecord
{
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
            [['send_bill'],'string','max'=>10],
            [['name', 'region'], 'string', 'max' => 50],
            [['address', 'bus_pic', 'logo','province','district','city'], 'string', 'max' => 128],
            [['merchant'], 'exist', 'skipOnError' => true, 'targetClass' => MerchantInfo::className(), 'targetAttribute' => ['merchant' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'name' => '门店名称',
            'wa_id' => '后台管理员id',
            'merchant' => '所属商户',
            'province' => '省',
            'city'=>'市',
            'district'=>'区',
            'region' => '所在地区',
            'address' => '详细地址',
            'lat' => '纬度',
            'lng' => '经度',
            'send_bill'=>'运费',
            'limit' => '配送范围',
            'bus_pic' => '营业执照',
            'logo' => '门店logo',
            'regist_at' => '入驻时间',
            'is_active' => '是否激活',
            'active_at' => '激活状态更改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getWa()
//    {
//        return $this->hasOne(WineAdmin::className(), ['wa_id' => 'wa_id']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerchant0()
    {
        return $this->hasOne(MerchantInfo::className(), ['id' => 'merchant']);
    }
}
