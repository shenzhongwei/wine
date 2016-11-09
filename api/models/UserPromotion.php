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
 * @property integer $pid
 * @property integer $add_at
 * @property integer $status
 * @property string $note
 *
 * @property UserInfo $u
 * @property PromotionInfo $p
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
            [['uid', 'type', 'target_id', 'add_at', 'status','pid'], 'integer'],
            [['note'],'string'],
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
            'type' => '类型 与promotion_type中的env相同',
            'target_id' => '对象id',
            'add_at' => '使用时间',
            'status' => '状态 1正常 0删除',
            'pid'=>'促销id',
            'note'=>'备注'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid']);
    }

    public function getP()
    {
        return $this->hasOne(PromotionInfo::className(), ['id' => 'pid']);
    }
}
