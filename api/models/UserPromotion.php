<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "user_promotion".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $type
 * @property integer $target_id
 * @property integer $add_at
 * @property integer $status
 *
 * @property UserInfo $u
 */
class UserPromotion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_promotion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'type', 'target_id', 'add_at', 'status'], 'integer'],
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
            'type' => '类型 1订单 2充值 3邀请',
            'target_id' => '对象id',
            'add_at' => '使用时间',
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
}
