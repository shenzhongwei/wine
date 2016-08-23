<?php

namespace api\models;

use Yii;

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
 * @property integer $send_id
 * @property string $pay_bill
 * @property integer $order_rate
 * @property integer $send_date
 * @property integer $is_del
 * @property integer $status
 *
 * @property OrderComment[] $orderComments
 * @property OrderDetail[] $orderDetails
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
            [['sid', 'uid', 'order_date', 'pay_id', 'pay_date', 'send_id', 'order_rate', 'send_date', 'is_del', 'status'], 'integer'],
            [['total', 'discount', 'send_bill', 'pay_bill'], 'number'],
            [['order_code'], 'string', 'max' => 16],
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
            'send_id' => '配送人id',
            'pay_bill' => '付款金额',
            'order_rate' => '订单进度',
            'send_date' => '送达时间',
            'is_del' => '是否已被用户删除',
            'status' => '状态 1正常 0后台删除',
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
