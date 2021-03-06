<?php

namespace admin\models;

use common\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "good_type".
 *
 * @property integer $id
 * @property string $name
 * @property integer $regist_at
 * @property string $logo
 * @property integer $is_active
 * @property integer $active_at
 *
 * @property GoodBoot[] $goodBoots
 * @property GoodBrand[] $goodBrands
 * @property GoodBreed[] $goodBreeds
 * @property GoodColor[] $goodColors
 * @property GoodCountry[] $goodCountries
 * @property GoodDry[] $goodDries
 * @property GoodInfo[] $goodInfos
 * @property GoodModel[] $goodModels
 * @property GoodPriceField[] $goodPriceFields
 * @property GoodSmell[] $goodSmells
 * @property GoodStyle[] $goodStyles
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
            [['name','logo'],'required','on'=>'create'],
            [['name'], 'string', 'max' => 25],
            [['logo'], 'string', 'max' => 255],
            [['name'],'validName','on'=>['create','update']]
        ];
    }

    public function scenarios()
    {
        $scen = parent::scenarios();
        $scen['create'] = ['id','name','regist_at','logo','is_active','active_at'];
        $scen['update'] = ['id','name','regist_at','logo','is_active','active_at'];
        return $scen;
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
            'logo' => '酒类图标',
            'is_active' => '是否上架',
            'active_at' => '上架状态更改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodBoots()
    {
        return $this->hasMany(GoodBoot::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodBrands()
    {
        return $this->hasMany(GoodBrand::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodBreeds()
    {
        return $this->hasMany(GoodBreed::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodColors()
    {
        return $this->hasMany(GoodColor::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodCountries()
    {
        return $this->hasMany(GoodCountry::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodDries()
    {
        return $this->hasMany(GoodDry::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodInfos()
    {
        return $this->hasMany(GoodInfo::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodModels()
    {
        return $this->hasMany(GoodModel::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodPriceFields()
    {
        return $this->hasMany(GoodPriceField::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodSmells()
    {
        return $this->hasMany(GoodSmell::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodStyles()
    {
        return $this->hasMany(GoodStyle::className(), ['type' => 'id']);
    }





    public static function GetTypes(){
        return ArrayHelper::map(self::find()->all(),'id','name');
    }


    public static function GetAllTypes(){
        return ArrayHelper::map(self::find()->all(),'name','name');
    }


    public static function GetChilds($type,$key){
        $type = self::findOne($type);
        return ArrayHelper::map($type->$key,'id','name');
    }

    public function validName(){
        $id = $this->id;
        $query = GoodType::find()->where("name=\"$this->name\"");
        if(!empty($id)){
            $query->andWhere("id<>$this->id");
        }
        $model = $query->one();
        if(!empty($model)){
            $this->addError('name',$this->name.'类型已存在');
        }
    }

    /*查询某一商户类型名称*/
    public static function getOneType($id){
        $model=GoodType::findOne($id);
        return empty($model)?'<span class="not-set">未设置</span>':$model->name;
    }
}
