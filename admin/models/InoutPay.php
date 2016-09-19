<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "inout_pay".
 *
 * @property integer $id
 * @property integer $inout_id
 * @property integer $uid
 * @property integer $pay_date
 * @property integer $pay_id
 * @property string $account
 * @property string $out_trade_no
 * @property string $transaction_id
 * @property string $money
 * @property integer $status
 *
 * @property AccountInout $inout
 */
class InoutPay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inout_pay';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inout_id', 'uid', 'pay_date', 'pay_id', 'status'], 'integer'],
            [['money'], 'number'],
            [['account', 'out_trade_no'], 'string', 'max' => 64],
            [['transaction_id'], 'string', 'max' => 32],
            [['inout_id'], 'exist', 'skipOnError' => true, 'targetClass' => AccountInout::className(), 'targetAttribute' => ['inout_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inout_id' => '明细ID',
            'uid' => '用户id',
            'pay_date' => '支付时间',
            'pay_id' => '支付方式',
            'account' => '支付账户',
            'out_trade_no' => '第三方订单号',
            'transaction_id' => '流水号',
            'money' => '实际支付金额',
            'status' => '状态',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInout()
    {
        return $this->hasOne(AccountInout::className(), ['id' => 'inout_id']);
    }
}
