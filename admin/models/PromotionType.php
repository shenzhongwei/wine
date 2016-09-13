<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "promotion_type".
 *
 * @property integer $id
 * @property integer $class
 * @property integer $group
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
            [['class', 'group', 'regist_at', 'is_active', 'active_at'], 'integer'],
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
            'class' => '类别',
            'group' => '组别',
            'name' => '优惠名称',
            'regist_at' => '添加时间',
            'is_active' => '是否上架',
            'active_at' => 'Active At',
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
