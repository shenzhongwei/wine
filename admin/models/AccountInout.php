<?php

namespace admin\models;

use Yii;

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
 * @property integer $status
 *
 * @property UserAccount $a
 * @property InoutPay[] $inoutPays
 */
class AccountInout extends \yii\db\ActiveRecord
{
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
            [['aid'], 'exist', 'skipOnError' => true, 'targetClass' => UserAccount::className(), 'targetAttribute' => ['aid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'aid' => '钱包id',
            'aio_date' => '生成时间',
            'type' => '类型',
            'target_id' => '订单/用户id',
            'sum' => '金额',
            'discount' => '赠送金额',
            'status' => '状态',
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
    public function getInoutPays()
    {
        return $this->hasOne(InoutPay::className(), ['inout_id' => 'id']);
    }
}