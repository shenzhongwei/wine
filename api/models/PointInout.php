<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "point_inout".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $pid
 * @property integer $pio_date
 * @property integer $pio_type
 * @property string $amount
 * @property integer $oid
 * @property string $note
 * @property integer $status
 *
 * @property UserInfo $u
 * @property UserPoint $p
 * @property OrderInfo $o
 */
class PointInout extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'point_inout';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'pid', 'pio_date', 'pio_type', 'oid', 'status'], 'integer'],
            [['amount'],'number'],
            [['note'],'string'],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['uid' => 'id']],
            [['pid'], 'exist', 'skipOnError' => true, 'targetClass' => UserPoint::className(), 'targetAttribute' => ['pid' => 'id']],
            [['oid'], 'exist', 'skipOnError' => true, 'targetClass' => OrderInfo::className(), 'targetAttribute' => ['oid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户id',
            'pid' => '积分账户id',
            'pio_date' => '生成时间',
            'pio_type' => '进出类型 1收入 2支出',
            'amount' => '进出数量',
            'oid' => '订单号',
            'note'=>'备注说明',
            'status' => '状态 1正常 0删除',
        ];
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
    public function getP()
    {
        return $this->hasOne(UserPoint::className(), ['id' => 'pid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getO()
    {
        return $this->hasOne(OrderInfo::className(), ['id' => 'oid']);
    }
}
