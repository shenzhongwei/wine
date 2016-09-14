<?php

namespace admin\models;

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
            'type' => 'Type',
            'id' => 'ID',
            'name' => 'Name',
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
}
