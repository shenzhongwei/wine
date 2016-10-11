<?php

namespace admin\models;

use common\helpers\ArrayHelper;
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
            [['name','logo','type'],'required','on'=>'create'],
            [['logo'], 'string', 'max' => 128],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => GoodType::className(), 'targetAttribute' => ['type' => 'id']],
            [['name'],'validName','on'=>['create','update']],
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
            'logo' => '品牌logo',
            'type' => '类型',
            'regist_at' => '添加时间',
            'is_active' => '是否上架',
            'active_at' => '上架状态更改时间',
        ];
    }

    public function scenarios()
    {
        $scen = parent::scenarios();
        $scen['create'] = ['id','name','regist_at','logo','is_active','active_at','type'];
        $scen['update'] = ['id','name','regist_at','logo','is_active','active_at','type'];
        return $scen;
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
        return $this->hasMany(GoodInfo::className(), ['brand' => 'id']);
    }

    public static function GetAllTypes(){
        return ArrayHelper::map(GoodType::find()->all(),'id','name');
    }

    public function validName(){
        $id = $this->id;
        $query = self::find()->where("name=\"$this->name\" and type=$this->type");
        if(!empty($id)){
            $query->andWhere("id<>$this->id");
        }
        $model = $query->one();
        if(!empty($model)){
            $this->addError('name',$this->name.'类型已存在');
        }
    }

    public static function GetBrands(){
        return ArrayHelper::map(self::find()->all(),'id','name');
    }

    public static function GetAllBrands($id){
        $brands = self::findAll(['type'=>$id]);
        return ArrayHelper::map($brands,'name','name');
    }

}
