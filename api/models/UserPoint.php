<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "user_point".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $point
 * @property integer $is_active
 * @property integer $create_at
 * @property integer $update_at
 *
 * @property PointInout[] $pointInouts
 * @property UserInfo $u
 */
class UserPoint extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_point';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'is_active', 'create_at', 'update_at'], 'integer'],
            [['point'],'string'],
            [['point'], 'required'],
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
            'point' => 'Point',
            'is_active' => '激活状态',
            'create_at' => '创建时间',
            'update_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPointInouts()
    {
        return $this->hasMany(PointInout::className(), ['pid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid']);
    }
}
