<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "good_brand".
 *
 * @property integer $id
 * @property string $name
 * @property string $logo
 * @property integer $type
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $active_at
 *
 * @property GoodType $type0
 * @property GoodInfo[] $goodInfos
 */
class GoodBrand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['logo'], 'string', 'max' => 128],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => GoodType::className(), 'targetAttribute' => ['type' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '品牌id',
            'name' => '品牌名',
            'logo' => '品牌log',
            'type' => '类型id',
            'regist_at' => '添加时间',
            'is_active' => '是否上架',
            'active_at' => '上架状态更改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(GoodType::className(), ['id' => 'type'])->where(['good_type.is_active'=>1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodInfos()
    {
        return $this->hasMany(GoodInfo::className(), ['boot' => 'id'])->where(['good_info.is_active'=>1]);
    }
}
