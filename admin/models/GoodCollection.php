<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "good_collection".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $gid
 * @property integer $add_at
 * @property integer $status
 *
 * @property GoodInfo $g
 * @property UserInfo $u
 */
class GoodCollection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_collection';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'gid', 'add_at', 'status'], 'integer'],
            [['gid'], 'exist', 'skipOnError' => true, 'targetClass' => GoodInfo::className(), 'targetAttribute' => ['gid' => 'id']],
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
            'gid' => '商品id',
            'add_at' => '添加时间',
            'status' => '状态 0已删除 1正常',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getG()
    {
        return $this->hasOne(GoodInfo::className(), ['id' => 'gid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid']);
    }


}
