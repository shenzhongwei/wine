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
            [['sid', 'uid', 'aid', 'order_date', 'pay_id', 'pay_date', 'ticket_id', 'send_id', 'state', 'send_date', 'is_del', 'status'], 'integer'],
            [['total', 'discount', 'send_bill', 'pay_bill'], 'number'],
            [['ticket_id'], 'required'],
            [['order_code'], 'string', 'max' => 16],
            [['send_code'], 'string', 'max' => 12],
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
            'sid' => 'Sid',
            'uid' => 'Uid',
            'aid' => 'Aid',
            'order_date' => 'Order Date',
            'order_code' => 'Order Code',
            'pay_id' => 'Pay ID',
            'pay_date' => 'Pay Date',
            'total' => 'Total',
            'discount' => 'Discount',
            'ticket_id' => 'Ticket ID',
            'send_bill' => 'Send Bill',
            'send_id' => 'Send ID',
            'send_code' => 'Send Code',
            'pay_bill' => 'Pay Bill',
            'state' => 'State',
            'send_date' => 'Send Date',
            'is_del' => 'Is Del',
            'status' => 'Status',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderPays()
    {
        return $this->hasMany(OrderPay::className(), ['oid' => 'id']);
    }
}
