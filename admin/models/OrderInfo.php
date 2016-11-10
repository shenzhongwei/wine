<?php

namespace admin\models;

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

    public $name;
    public $nickname;
    public $is_ticket;
    public $order_date_from;
    public $order_date_to;
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
            [['ticket_id'], 'required'],
            [['order_code'], 'string', 'max' => 16],
            [['send_code'], 'string', 'max' => 12],
            [['aid'], 'exist', 'skipOnError' => true, 'targetClass' => UserAddress::className(), 'targetAttribute' => ['aid' => 'id']],
            [['send_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmployeeInfo::className(), 'targetAttribute' => ['send_id' => 'id']],
            [['sid'], 'exist', 'skipOnError' => true, 'targetClass' => ShopInfo::className(), 'targetAttribute' => ['sid' => 'id']],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['uid' => 'id']],

            [['name'],'string','max'=>50]
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
            'status' => '订单状态',
            'type'=>'购买类型 1普通商品 2会员 3抢购',
            'name' => '门店名',
            'nickname' => '用户名',
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

}
