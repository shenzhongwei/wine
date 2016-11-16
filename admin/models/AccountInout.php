<?php

namespace admin\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "account_inout".
 *
 * @property integer $id
 * @property integer $aid
 * @property integer $aio_date
 * @property integer $type
 * @property integer $target_id
 * @property string $sum
 * @property string $discount
 * @property string $note
 * @property integer $status
 *
 * @property UserAccount $a
 * @property InoutPay $inoutPay
 */
class AccountInout extends ActiveRecord
{
    public $pay_id;
    public $pay_date;
    public $phone;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_inout';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aid', 'aio_date', 'type', 'target_id', 'status'], 'integer'],
            [['sum', 'discount'], 'number'],
            [['note'], 'string', 'max' => 255],
            [['aid'], 'exist', 'skipOnError' => true, 'targetClass' => UserAccount::className(), 'targetAttribute' => ['aid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'aid' => '钱包id',
            'aio_date' => '生成时间',
            'type' => '类型 1订单支付 2订单收入 3活动奖励 4余额充值',
            'target_id' => '发起对象id',
            'sum' => '金额',
            'discount' => '赠送金额',
            'note' => '备注',
            'status' => '状态 0删除 1正常 2待付款',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getA()
    {
        return $this->hasOne(UserAccount::className(), ['id' => 'aid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInoutPay()
    {
        return $this->hasOne(InoutPay::className(), ['inout_id' => 'id']);
    }



}
