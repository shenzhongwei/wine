<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "good_type".
 *
 * @property integer $id
 * @property string $name
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $active_at
 *
 * @property GoodInfo[] $goodInfos
 */
class GoodType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['regist_at', 'is_active', 'active_at'], 'integer'],
            [['name'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '类型id',
            'name' => '类型名称',
            'regist_at' => '添加时间',
            'is_active' => '是否上架',
            'active_at' => '上架状态更改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodInfos()
    {
        return $this->hasMany(GoodInfo::className(), ['type' => 'id']);
    }
}
