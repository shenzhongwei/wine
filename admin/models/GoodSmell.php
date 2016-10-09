<?php

namespace admin\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "good_smell".
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $active_at
 *
 * @property GoodInfo[] $goodInfos
 * @property GoodType $type0
 */
class GoodSmell extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_smell';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['type','name'],'required'],
            [['name'], 'string', 'max' => 50],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => GoodType::className(), 'targetAttribute' => ['type' => 'id']],
            [['name'],'validName'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '酒香id',
            'type' => '类型',
            'name' => '酒香名称',
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
        return $this->hasMany(GoodInfo::className(), ['smell' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(GoodType::className(), ['id' => 'type']);
    }

    public function validName(){
        $id = $this->id;
        $query = self::find()->where("name=\"$this->name\" and type=$this->type");
        if(!empty($id)){
            $query->andWhere("id<>$this->id");
        }
        $model = $query->one();
        if(!empty($model)){
            $this->addError('name', '香型' . $this->name . '已存在');
        }
    }
    public static function GetAllTypes(){
        return ArrayHelper::map(GoodType::find()->all(),'id','name');
    }

    public static function GetAllSmells($id){
        $brands = self::findAll(['type'=>$id]);
        return ArrayHelper::map($brands,'name','name');
    }

}
