<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "order_comment".
 *
 * @property integer $id
 * @property integer $oid
 * @property integer $uid
 * @property integer $send_star
 * @property integer $add_at
 * @property integer $status
 *
 * @property CommentDetail[] $commentDetails
 * @property OrderInfo $o
 * @property UserInfo $u
 */
class OrderComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oid', 'uid', 'send_star', 'add_at', 'status'], 'integer'],
            [['oid'], 'exist', 'skipOnError' => true, 'targetClass' => OrderInfo::className(), 'targetAttribute' => ['oid' => 'id']],
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
            'oid' => '订单',
            'uid' => '用户',
            'send_star' => '送货评价',
            'add_at' => '提交时间',
            'status' => '状态 0删除 1正常',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommentDetails()
    {
        return $this->hasMany(CommentDetail::className(), ['cid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getO()
    {
        return $this->hasOne(OrderInfo::className(), ['id' => 'oid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid']);
    }
}
