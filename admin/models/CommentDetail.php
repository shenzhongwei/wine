<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "comment_detail".
 *
 * @property integer $id
 * @property integer $cid
 * @property integer $gid
 * @property integer $star
 * @property string $content
 * @property integer $status
 *
 * @property GoodInfo $g
 * @property OrderComment $c
 */
class CommentDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'gid', 'star', 'status'], 'integer'],
            [['content'], 'string', 'max' => 250],
            [['gid'], 'exist', 'skipOnError' => true, 'targetClass' => GoodInfo::className(), 'targetAttribute' => ['gid' => 'id']],
            [['cid'], 'exist', 'skipOnError' => true, 'targetClass' => OrderComment::className(), 'targetAttribute' => ['cid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cid' => '评论id',
            'gid' => '商品id',
            'star' => '评分0-5',
            'content' => '评价',
            'status' => '状态0删除 1正常',
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
    public function getC()
    {
        return $this->hasOne(OrderComment::className(), ['id' => 'cid']);
    }

    /**
     * @inheritdoc
     * @return CommentDetailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CommentDetailQuery(get_called_class());
    }
}
