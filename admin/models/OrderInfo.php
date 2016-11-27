<?php

namespace admin\models;

use common\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "order_info".
 *
 * @property integer $id
 * @property integer $sid
 * @property integer $uid
 * @property integer $aid
 * @property integer $order_date
 * @property string $order_code
 * @property integer $type
 * @property string $point
 * @property integer $pay_id
 * @property integer $pay_date
 * @property string $total
 * @property string $discount
 * @property integer $ticket_id
 * @property string $send_bill
 * @property integer $send_id
 * @property string $send_code
 * @property string $pay_bill
 * @property integer $state
 * @property integer $send_date
 * @property integer $is_del
 * @property integer $status
 *
 * @property OrderComment[] $orderComments
 * @property OrderDetail[] $orderDetails
 * @property UserAddress $a
 * @property EmployeeInfo $send
 * @property ShopInfo $s
 * @property UserInfo $u
 * @property OrderPay[] $orderPays
 */
class OrderInfo extends \yii\db\ActiveRecord
{

    public $username;
    public $disc;
    public $step;
    public $is_ticket;
    public $is_point;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sid', 'uid', 'aid', 'order_date', 'pay_id', 'pay_date', 'ticket_id', 'type','state', 'send_date', 'is_del', 'status'], 'integer'],
            [['total', 'discount', 'send_bill', 'pay_bill','point'], 'number'],
            [['aid'], 'exist', 'skipOnError' => true, 'targetClass' => UserAddress::className(), 'targetAttribute' => ['aid' => 'id']],
            [['send_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmployeeInfo::className(), 'targetAttribute' => ['send_id' => 'id']],
            [['sid'], 'exist', 'skipOnError' => true, 'targetClass' => ShopInfo::className(), 'targetAttribute' => ['sid' => 'id']],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['uid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sid' => '门店id',
            'uid' => '用户id',
            'aid' => '用户地址id',
            'order_date' => '下单时间',
            'order_code' => '订单号',
            'pay_id' => '支付方式',
            'pay_date' => '支付时间',
            'total' => '总金额',
            'disc'=>'优惠金额',
            'discount' => '优惠金额',
            'ticket_id' => '优惠券id',
            'send_bill' => '配送费',
            'send_id' => '配送人员id',
            'send_code' => '物流编号',
            'pay_bill' => '付款金额',
            'state' => '订单进度',
            'send_date' => '送达时间',
            'point'=>'使用积分',
            'is_del' => '是否删除',
            'step'=>'订单进度',
            'status' => '订单状态',
            'type'=>'购买类型 1普通商品 2会员 3抢购',
            'username' => '用户手机',
            'is_ticket' => '是否使用优惠券'

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderComments()
    {
        return $this->hasMany(OrderComment::className(), ['oid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderDetails()
    {
        return $this->hasMany(OrderDetail::className(), ['oid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getA()
    {
        return $this->hasOne(UserAddress::className(), ['id' => 'aid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSend()
    {
        return $this->hasOne(EmployeeInfo::className(), ['id' => 'send_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getS()
    {
        return $this->hasOne(ShopInfo::className(), ['id' => 'sid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid']);
    }

    public static function GetUsernames(){
        $query = self::Query();
        $userNames = $query->joinWith('u')->addSelect('user_info.phone as username')->where("user_info.id>0 and order_info.id>0")->all();
        return array_values(array_unique(ArrayHelper::getColumn($userNames,'username')));
    }

    public static function GetOrderCodes(){
        $query = self::Query();
        $orderCodes = $query->all();
        return array_values(ArrayHelper::getColumn($orderCodes,'order_code'));
    }

    public static function getShopNames(){
        $admin = Yii::$app->user->identity;
        $admin_type = $admin->wa_type;
        $admin_id = $admin->id;
        $query = ShopInfo::find()->joinWith('orderInfos')->where("order_info.sid>0");
        if($admin_type==3){
            $manager = MerchantInfo::findOne(['wa_id'=>$admin_id]);
            if(!empty($manager)){
                $shops = ShopInfo::find()->where(['merchant'=>$manager->id])->all();
                $idArr = array_values(ArrayHelper::getColumn($shops,'id'));
                if(!empty($idArr)){
                    $query->andWhere("order_info.sid in (".implode(',',$idArr).")");
                }else{
                    $query->andWhere('order_info.sid=0');
                }
            }else{
                $query->andWhere('order_info.sid=0');
            }
        }
        $shops = $query->all();
        return ArrayHelper::map($shops,'id','name');
    }

    public static function Query(){
        $admin = Yii::$app->user->identity;
        $admin_type = $admin->wa_type;
        $admin_id = $admin->id;
        $query = OrderInfo::find();
        if($admin_type==3){
            $manager = MerchantInfo::findOne(['wa_id'=>$admin_id]);
            if(!empty($manager)){
                $shops = ShopInfo::find()->where(['merchant'=>$manager->id])->all();
                $idArr = array_values(ArrayHelper::getColumn($shops,'id'));
                if(!empty($idArr)){
                    $query->andWhere("sid in (".implode(',',$idArr).")");
                }else{
                    $query->andWhere('sid=0');
                }
            }else{
                $query->andWhere('sid=0');
            }
        }elseif ($admin_type==4){
            $manager = ShopInfo::findOne(['wa_id'=>$admin_id]);
            if(!empty($manager)){
                $query->andWhere("sid=$manager->id");
            }else{
                $query->andWhere('sid=0');
            }
        }
        return $query;
    }


    /*
     * 获取支付方式
     */
    public static function getPaytype($pay_id=0){
        if(empty($pay_id)){
            $model=Dics::find()->select(['id','name'])->where(['type'=>'付款方式'])->asArray()->all();
            return empty($model)?[]:$model;
        }else{
            $model=Dics::find()->select(['name'])->where(['type'=>'付款方式','id'=>$pay_id])->asArray()->one();
            return empty($model)?'<span class="not-set">未知方式</span>':$model['name'];
        }

    }

    /*
     *获取配送进度
     */
    public static function getOrderstep($state){
        $model=Dics::find()->select(['name'])->where(['type'=>'订单状态','id'=>$state])->asArray()->one();
        return empty($model)?'<span class="not-set">未知状态</span>':$model['name'];
    }
}
