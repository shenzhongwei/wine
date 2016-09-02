<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "good_color".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $active_at
 *
 * @property GoodType $type0
 * @property GoodInfo[] $goodInfos
 */
class GoodColor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_color';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => GoodType::className(), 'targetAttribute' => ['type' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'name' => '颜色类型',
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
        return $this->hasOne(GoodType::className(), ['id' => 'type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodInfos()
    {
        return $this->hasMany(GoodInfo::className(), ['color' => 'id']);
    }

    /**
     * @inheritdoc
     * @return GoodColorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GoodColorQuery(get_called_class());
    }
}
