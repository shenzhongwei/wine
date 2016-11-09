<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "ticket_inout".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $tid
 * @property integer $regist_at
 * @property integer $status
 * @property string $note
 *
 * @property UserInfo $u
 * @property UserTicket $t
 */
class TicketInout extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_inout';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'tid','regist_at','status'], 'integer'],
            [['note'], 'string', 'max' => 225],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['uid' => 'id']],
            [['tid'], 'exist', 'skipOnError' => true, 'targetClass' => UserTicket::className(), 'targetAttribute' => ['tid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'uid' => '用户id',
            'tid' => '票据id',
            'note' => '备注',
            'regist_at'=>'领取时间',
            'status'=>'状态',
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
    public function getT()
    {
        return $this->hasOne(UserTicket::className(), ['id' => 'tid']);
    }
}
