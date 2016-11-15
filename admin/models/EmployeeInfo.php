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
    public $num;
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

    /**
     * 获取所有可以配送人员列表
     */
    public static function GetAllEmployee(){
        $admin = Yii::$app->user->identity;
        $admin_type = $admin->wa_type;
        $admin_id = $admin->id;
        $query = self::find()->where("`status`=1");
        if($admin_type==3){
            $manager = MerchantInfo::findOne(['wa_id'=>$admin_id]);
            if(!empty($manager)){
                $shops = ShopInfo::find()->where(['merchant'=>$manager->id])->all();
                $idArr = array_values(ArrayHelper::getColumn($shops,'id'));
                if(!empty($idArr)){
                    $query->andWhere("owner_id in (".implode(',',$idArr).")");
                }else{
                    $query->andWhere('owner_id=0');
                }
            }else{
                $query->andWhere('owner_id=0');
            }
        }elseif ($admin_type==4){
            $manager = ShopInfo::findOne(['wa_id'=>$admin_id]);
            if(!empty($manager)){
                $query->andWhere("owner_id=$manager->id");
            }else{
                $query->andWhere('owner_id=0');
            }
        }
        $query->leftJoin(
            "(SELECT send_id,count(*) AS num FROM order_info WHERE state=4 GROUP BY send_id) a",'a.send_id=employee_info.id');
        $query->addSelect(['employee_info.*','IFNULL(num,0) AS num']);
        $employees = $query->all();
        if(empty($employees)){
            return [
                0=>'暂无待发中的配送员'
            ];
        }else{
            return ArrayHelper::map(ArrayHelper::getColumn($employees,function($element){
                return [
                    'id'=>$element->id,
                    'name'=>$element->name."(待配送订单数：$element->num)"
                ];
            }),'id','name');
        }
    }

}
