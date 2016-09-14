<?php

namespace admin\models;

use common\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "employee_info".
 *
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property integer $type
 * @property integer $owner_id
 * @property integer $register_at
 * @property integer $status
 *
 * @property OrderInfo[] $orderInfos
 */
class EmployeeInfo extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employee_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'owner_id', 'register_at', 'status'], 'integer'],
            [['name'], 'string', 'max' => 25],
            [['phone'], 'string', 'max' => 11],

            [['phone','name','type','owner_id'],'required'],
            ['phone','match','pattern'=>'/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9][0-9]{8}|17[0-9]{9}$|14[0-9]{9}$/','message'=>'手机号格式不正确'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '配送人员姓名',
            'phone' => '配送人员联系方式',
            'type' => '所属类型',
            'owner_id' => 'Owner ID',
            'register_at' => 'Register At',
            'status' => '当前状态',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfos()
    {
        return $this->hasMany(OrderInfo::className(), ['send_id' => 'id']);
    }

    /*
     * 获取所有的配送人员
     */
    public static function getAllemployee(){
        $model=self::find()->select(['id','name'])->andWhere(['!=','status',0])->asArray()->all();
        $query=array();
        foreach($model as $k=>$v){
            $query[$v['id']]=$v['name'];
        }
            return $query;
    }

    public static function getOwners($type){
        switch($type){
            case 0: //商家
                $model=MerchantInfoSearch::find()->where(['is_active'=>1])->all();
                break;
            case 1: //门店
                $model=ShopSearch::find()->where(['is_active'=>1])->all();
                break;
            default://wu
                $model = [];
                break;
        }
        $results=[];
        if(!empty($model)){
            $results = ArrayHelper::map($model,'id','name');
        }
        return $results;
    }

}
