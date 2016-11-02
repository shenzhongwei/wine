<?php

namespace admin\models;

use common\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "dics".
 *
 * @property string $type
 * @property integer $id
 * @property string $name
 */
class Dics extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dics';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type', 'name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => '类型',
            'id' => 'id',
            'name' => '名称',
        ];
    }

    /*
     * 获取所有的订单状态
     */
    public static function getAllorderstate(){
        $model=Dics::find()->select(['id','name'])->andWhere(['type'=>'订单状态'])->asArray()->all();
        $query=array();
        foreach($model as $k=>$v){
            $query[$v['id']]=$v['name'];
        }
        return $query;
    }


    /*
     * 获取优惠适用对象
     */
    public static function getPromotionRange(){
        $model=Dics::find()->select(['id','name'])->andWhere(['type'=>'优惠适用对象'])->asArray()->all();
        $query=array();
        foreach($model as $k=>$v){
            $query[$v['id']]=$v['name'];
        }
        return $query;
    }

    /*
     * 获取钱包类型
     */
    public static function getAccountType(){
        $model=Dics::find()->select(['id','name'])->andWhere(['type'=>'钱包类型'])->asArray()->all();
        $query=array();
        foreach($model as $k=>$v){
            $query[$v['id']]=$v['name'];
        }
        return $query;
    }

    /*
     * 获取图片类型
     */
    public static function getPicType($type){
        $query=Dics::find()->select(['id','name'])->andWhere("type='图片类型'");
        if($type<>7){
            $query->andWhere("name<>'启动页'");
        }
        $query->orderBy(['id'=>SORT_ASC]);
        $model = $query->all();
        $res = ArrayHelper::map($model,'id','name');
        return $res;
    }

    /*
     * 获取图片位置
     */
    public static function getPicPos(){
        $model=Dics::find()->select(['id','name'])->andWhere(['type'=>'广告图位置'])->orderBy(['id'=>SORT_ASC])->all();
        $res = ArrayHelper::map($model,'id','name');
        return $res;
    }

    /*
     * 消息类型
     */
    public static function getMessageType(){
        $model=Dics::find()->select(['id','name'])->andWhere(['type'=>'消息类型'])->asArray()->all();
        $query=array();
        foreach($model as $k=>$v){
            $query[$v['id']]=$v['name'];
        }
        return $query;
    }

    /*
     * 消息跳转页面
     */
    public static function getMessageToUrl(){
        $model=Dics::find()->select(['id','name'])->andWhere(['type'=>'消息跳转页面'])->asArray()->all();
        $query=array();
        foreach($model as $k=>$v){
            $query[$v['id']]=$v['name'];
        }
        return $query;
    }


    public static function getPromotionClass(){
        $data = self::find()->where(['type'=>'促销类别'])->all();
        return ArrayHelper::map($data,'id','name');
    }

    public static function getPromotion(){
        $data = self::find()->where(['type'=>'促销类别'])->all();
        return ArrayHelper::map($data,'id','name');
    }

    public static function getPromotionEnv(){
        $data = self::find()->where(['type'=>'促销环境'])->all();
        return ArrayHelper::map($data,'id','name');
    }

    public static function getPromotionGroup(){
        $data = self::find()->where(['type'=>'促销形式'])->all();
        return ArrayHelper::map($data,'id','name');
    }

    public static function getPromotionLimit(){
        $data = self::find()->where(['type'=>'促销限制'])->all();
        return ArrayHelper::map($data,'id','name');
    }
}
