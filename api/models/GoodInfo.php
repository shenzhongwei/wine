<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "good_info".
 *
 * @property integer $id
 * @property integer $merchant
 * @property integer $type
 * @property integer $brand
 * @property integer $smell
 * @property integer $boot
 * @property string $name
 * @property string $volum
 * @property string $price
 * @property string $unit
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $active_at
 *
 * @property MerchantInfo $merchant0
 * @property GoodBrand $brand0
 * @property GoodSmell $smell0
 * @property GoodType $type0
 * @property GoodRush[] $goodRushes
 * @property GoodVip[] $goodVips
 */
class GoodInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['merchant', 'type', 'brand', 'smell', 'boot', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['volum'], 'string', 'max' => 128],
            [['unit'], 'string', 'max' => 10],
            [['merchant'], 'exist', 'skipOnError' => true, 'targetClass' => MerchantInfo::className(), 'targetAttribute' => ['merchant' => 'id']],
            [['brand'], 'exist', 'skipOnError' => true, 'targetClass' => GoodBrand::className(), 'targetAttribute' => ['brand' => 'id']],
            [['smell'], 'exist', 'skipOnError' => true, 'targetClass' => GoodSmell::className(), 'targetAttribute' => ['smell' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => GoodType::className(), 'targetAttribute' => ['type' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'merchant' => '所属商户',
            'type' => '类型',
            'brand' => '品牌',
            'smell' => '香型',
            'boot' => '产地',
            'name' => '商品名',
            'volum' => '容量',
            'price' => '价格',
            'unit' => '单位',
            'regist_at' => '添加时间',
            'is_active' => '是否上架',
            'active_at' => '上架状态更改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerchant0()
    {
        return $this->hasOne(MerchantInfo::className(), ['id' => 'merchant']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand0()
    {
        return $this->hasOne(GoodBrand::className(), ['id' => 'brand']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSmell0()
    {
        return $this->hasOne(GoodSmell::className(), ['id' => 'smell']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(GoodType::className(), ['id' => 'type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodRushes()
    {
        return $this->hasMany(GoodRush::className(), ['gid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodVips()
    {
        return $this->hasMany(GoodVip::className(), ['gid' => 'id']);
    }
}
