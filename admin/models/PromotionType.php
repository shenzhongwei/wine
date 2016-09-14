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


    /*
     * 获取优惠券的类别
     */
    public static function getPromotionTypes($model){
        switch($model->class){
            case 1: $str='<span style="color: #00a2d4">有券</span>'; break;
            case 2: $str='<span style="color: #47a447">无券</span>'; break;
            default: $str='<span style="color: red">类别错误</span>'; break;
        }
        return $str;
    }


    /*
     * 获取优惠券的组别
     */
    public static function getPromotionGroup($model){
        switch($model->group){
            case 1: $str='<span style="color: #ff674c">优惠</span>'; break;
            case 2: $str='<span style="color: #803f1e">特权</span>'; break;
            case 3: $str='<span style="color: #202020">赠送</span>'; break;
            default: $str='<span style="color: red">组别错误</span>'; break;
        }
        return $str;
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
