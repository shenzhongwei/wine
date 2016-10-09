<?php

namespace admin\models;

use common\helpers\ArrayHelper;
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
            [['name','type'],'required'],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => GoodType::className(), 'targetAttribute' => ['type' => 'id']],
            [['name'], 'validName'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'name' => '色型名',
            'type' => '类型',
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

    public function validName()
    {
        $id = $this->id;
        $query = self::find()->where("name=\"$this->name\" and type=$this->type");
        if (!empty($id)) {
            $query->andWhere("id<>$this->id");
        }
        $model = $query->one();
        if (!empty($model)) {
            $this->addError('name', '色型' . $this->name . '已存在');
        }
    }
    public static function GetAllTypes()
    {
        return ArrayHelper::map(GoodType::find()->all(), 'id', 'name');
    }

    public static function GetAllColors($id)
    {
        $brands = self::findAll(['type' => $id]);
        return ArrayHelper::map($brands, 'name', 'name');
    }

}
