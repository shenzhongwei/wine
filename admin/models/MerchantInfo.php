<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "merchant_info".
 *
 * @property integer $id
 * @property string $name
 * @property integer $wa_id
 * @property string $region
 * @property string $address
 * @property integer $lat
 * @property integer $lng
 * @property integer $registe_at
 * @property integer $is_active
 * @property integer $active_at
 * @property string $province
 * @property string $phone
 * @property string $city
 * @property string $district
 *
 * @property GoodInfo[] $goodInfos
 * @property Admin $wa
 * @property ShopInfo[] $shopInfos
 */
class MerchantInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'merchant_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wa_id', 'lat', 'lng', 'registe_at', 'is_active', 'active_at'], 'integer'],
            [['name', 'address', 'province', 'city', 'district'], 'string', 'max' => 128],
            [['phone'], 'string', 'max' => 11],
            [['region'], 'string', 'max' => 50],
            [['wa_id'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::className(), 'targetAttribute' => ['wa_id' => 'wa_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'name' => '商户名',
            'wa_id' => '后台管理员id',
            'region' => '所在地区',
            'address' => '详细地址',
            'lat' => '纬度',
            'phone'=>'手机号',
            'lng' => '经度',
            'registe_at' => '入驻时间',
            'is_active' => '是否激活',
            'active_at' => '激活状态更改时间',
            'province' => '省',
            'city' => '市',
            'district' => '区',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodInfos()
    {
        return $this->hasMany(GoodInfo::className(), ['merchant' => 'id']);
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
    public function getShopInfos()
    {
        return $this->hasMany(ShopInfo::className(), ['merchant' => 'id']);
    }

    /**
     * @inheritdoc
     * @return MerchantInfoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MerchantInfoQuery(get_called_class());
    }
}
