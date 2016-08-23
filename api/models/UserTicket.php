<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "user_ticket".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $pid
 * @property integer $start_at
 * @property integer $end_at
 * @property integer $status
 *
 * @property PromotionInfo $p
 * @property UserInfo $u
 */
class UserTicket extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'pid', 'start_at', 'end_at', 'status'], 'integer'],
            [['pid'], 'exist', 'skipOnError' => true, 'targetClass' => PromotionInfo::className(), 'targetAttribute' => ['pid' => 'id']],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['uid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'uid' => '用户id',
            'pid' => '促销id',
            'start_at' => '有效开始时间',
            'end_at' => '结束时间',
            'status' => '1正常 0过期 2已使用',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getP()
    {
        return $this->hasOne(PromotionInfo::className(), ['id' => 'pid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid']);
    }
}
