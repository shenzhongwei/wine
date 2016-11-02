<?php

namespace admin\models;

use common\helpers\ArrayHelper;
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
            [['env', 'class', 'group', 'limit','name'],'required'],
            [['name'], 'string', 'max' => 128],
            [['env', 'class', 'group', 'limit'],'validType'],
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

    public function validType(){
        $query = self::find()->where(['env'=>$this->env,'class'=>$this->class,'group'=>$this->group,'limit'=>$this->limit]);
        if(!empty($this->id)){
            $query->andWhere("id<>$this->id");
        }
        $model = $query->one();
        if(!empty($model)){
            $this->addError('limit','已存在组别、环境、形式和限制完全相同的促销类型，请勿重复操作');
        }
    }


    /*
     * 获取优惠券的类别
     */
    public static function getPromotionClass($class){
        $res = Dics::findOne(['type'=>'促销类别','id'=>$class]);
        return empty($res) ? '<span class="not-set">未设置</span>':$res->name;
    }


    /**
     * @param $model
     * @return string
     * 获取促销环境
     */
    public static function getPromotionEnv($env){
        $res = Dics::findOne(['type'=>'促销环境','id'=>$env]);
        return empty($res) ? '<span class="not-set">未设置</span>':$res->name;
    }


    /*
     * 获取优惠的形式
     */
    public static function getPromotionGroup($group){
        $res = Dics::findOne(['type'=>'促销形式','id'=>$group]);
        return empty($res) ? '<span class="not-set">未设置</span>':$res->name;
    }


    /*
     * 获取优惠的形式
     */
    public static function getPromotionLimit($limit){
        $res = Dics::findOne(['type'=>'促销限制','id'=>$limit]);
        return empty($res) ? '<span class="not-set">未设置</span>':$res->name;
    }

    public static function GetNames(){
        $res = array_values(ArrayHelper::getColumn(self::find()->all(),'name'));
        return $res;
    }

    /*
     * 获取优惠券的分类名称
     */
    public static function getPromotionTypeName($id){
        $model=self::findOne(['id'=>$id]);
        return empty($model)?'':$model->name;
    }

    /*
     * 根据id去dics查找优惠对象
     */
    public static function getPromotionRangeById($id){
        $model=Dics::find()->select(['name'])->andWhere(['type'=>'优惠适用对象','id'=>$id])->asArray()->one();
        return empty($model)?'':$model['name'];
    }
}
