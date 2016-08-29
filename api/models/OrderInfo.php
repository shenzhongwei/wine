<?php

namespace api\models;

use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "order_info".
 *
 * @property integer $id
 * @property integer $sid
 * @property integer $uid
 * @property integer $order_date
 * @property string $order_code
 * @property integer $pay_id
 * @property integer $pay_date
 * @property string $total
 * @property string $discount
 * @property string $send_bill
 * @property integer $ticket_id
 * @property integer $send_id
 * @property string $pay_bill
 * @property string $send_code
 * @property integer $state
 * @property integer $send_date
 * @property integer $is_del
 * @property integer $status
 *
 * @property OrderComment $orderComment
 * @property OrderDetail[] $orderDetails
 * @property EmployeeInfo $send
 * @property ShopInfo $s
 * @property UserTicket $ticket
 * @property UserInfo $u
 * @property OrderPay $orderPay
 */
class OrderInfo extends \yii\db\ActiveRecord
{
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
            [['sid', 'uid', 'order_date', 'pay_id', 'pay_date', 'send_id', 'state', 'send_date', 'is_del', 'status','ticket_id'], 'integer'],
            [['total', 'discount', 'send_bill', 'pay_bill'], 'number'],
            [['order_code'], 'string', 'max' => 16],
            [['send_code'],'string','max'=>12],
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
            'sid' => '店铺id',
            'uid' => '用户',
            'order_date' => '下单时间',
            'order_code' => '订单编码',
            'pay_id' => '支付方式',
            'pay_date' => '付款时间',
            'total' => '总价',
            'discount' => '优惠金额',
            'send_bill' => '运费',
            'send_code'=>'物流编号',
            'ticket_id'=>'优惠券',
            'send_id' => '配送人id',
            'pay_bill' => '付款金额',
            'state' => '订单进度',
            'send_date' => '送达时间',
            'is_del' => '是否已被用户删除',
            'status' => '状态 1正常 0后台删除',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderComment()
    {
        return $this->hasOne(OrderComment::className(), ['oid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(UserTicket::className(), ['id' => 'ticket_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderDetails()
    {
        return $this->hasMany(OrderDetail::className(), ['oid' => 'id'])->joinWith('g')->where(['and','gid>0','good_info.id>0']);
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderPay()
    {
        return $this->hasOne(OrderPay::className(), ['oid' => 'id']);
    }

    //生成订单码
    public static function generateCode(){
        $arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $key = array_rand($arr);
        $code=$arr[$key];
        return $code;
    }

    //自动取消订单
    public static function AutoCancelOrder($user_id){
        $userOrders = self::find()->where(['and',"uid=$user_id",'state=1','order_date<'.(time()-900)])->all();
        if(!empty($userOrders)){
            $transaction = Yii::$app->db->beginTransaction();
            try{
                foreach($userOrders as $userOrder) {
                    //还原优惠券
                    if(!empty($userOrder->ticket_id)){
                        $ticket_id = $userOrder->ticket_id;
                        $user_ticket = UserTicket::findOne(['id'=>$ticket_id,'uid'=>$user_id]);
                        if(!empty($user_ticket)){
                            $user_ticket->status = 1;
                            if(!$user_ticket->save()){
                                throw new Exception('修改订单状态失败');
                            }
                        }
                    }
                    $userOrder->state = 100;//修改字段
                    $userOrder->ticket_id = 0;
                    if(!$userOrder->save()){
                        throw new Exception('取消订单失败');
                    }
                }
                $transaction->commit();
                return true;
            }catch (Exception $e){
                $transaction->rollBack();
                return false;
            }
        }else{
            return true;
        }
    }
}
