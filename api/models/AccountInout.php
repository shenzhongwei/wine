<?php

namespace api\models;

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
 * @property string $note
 * @property string $discount
 * @property integer $status
 *
 * @property UserAccount $a
 * @property InoutPay $inoutPay
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
            [['sum','discount'], 'number'],
            [['note'], 'string'],
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
            'type' => '类型',
            'target_id' => '对象id',
            'sum' => '金额',
            'discount' => '赠送金额',
            'note'=>'备注',
            'status' => '状态 0删除 1正常',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getA()
    {
        return $this->hasOne(UserAccount::className(), ['id' => 'aid'])->where(['user_account.is_active'=>1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInoutPay()
    {
        return $this->hasOne(InoutPay::className(), ['inout_id' => 'id']);
    }
}
