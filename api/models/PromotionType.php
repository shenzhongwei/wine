<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "promotion_type".
 *
 * @property integer $id
 * @property integer $env
 * @property integer $class
 * @property integer $group
 * @property integer $limit
 * @property string $name
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $active_at
 *
 * @property PromotionInfo[] $promotionInfos
 */
class PromotionType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promotion_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['env', 'class', 'group', 'limit', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'env' => '促销环境',
            'class' => '类别',
            'group' => '形式',
            'limit' => '促销限制',
            'name' => '优惠名称',
            'regist_at' => '添加时间',
            'is_active' => '是否上架',
            'active_at' => '上架状态更改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionInfos()
    {
        return $this->hasMany(PromotionInfo::className(), ['pt_id' => 'id']);
    }
}
