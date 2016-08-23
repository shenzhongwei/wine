<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "order_pay".
 *
 * @property integer $id
 * @property integer $oid
 * @property integer $uid
 * @property integer $pay_date
 * @property integer $pay_id
 * @property string $account
 * @property string $out_trade_no
 * @property string $transaction_id
 * @property string $money
 * @property integer $status
 *
 * @property OrderInfo $o
 */
class OrderPay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_pay';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oid', 'uid', 'pay_date', 'pay_id', 'status'], 'integer'],
            [['money'], 'number'],
            [['account', 'out_trade_no'], 'string', 'max' => 64],
            [['transaction_id'], 'string', 'max' => 32],
            [['oid'], 'exist', 'skipOnError' => true, 'targetClass' => OrderInfo::className(), 'targetAttribute' => ['oid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'oid' => '订单id',
            'uid' => '用户id',
            'pay_date' => '付款时间',
            'pay_id' => '付款方式',
            'account' => '用户支付帐户（如微信openid,支付宝id)',
            'out_trade_no' => '返回的第三方订单号',
            'transaction_id' => '支付通道返回的交易流水号',
            'money' => '实际支付金额',
            'status' => '状态',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getO()
    {
        return $this->hasOne(OrderInfo::className(), ['id' => 'oid']);
    }
}
