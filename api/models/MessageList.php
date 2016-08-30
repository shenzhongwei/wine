<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "message_list".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $title
 * @property string $content
 * @property integer $own_id
 * @property integer $target
 * @property integer $status
 * @property string $publish_at
 *
 * @property OrderInfo $order
 * @property UserInfo $user
 */
class MessageList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'own_id', 'target', 'status'], 'integer'],
            [['publish_at'], 'string','max'=>20],
            [['title'], 'string', 'max' => 50],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'type_id' => '类型id 1系统消息 2用户消息 3订单消息',
            'title' => '标题',
            'content' => '内容',
            'own_id' => '所属id，根据消息类型id判断',
            'target' => '目标id 跳转页面',
            'status' => '状态 1未读 0已读',
            'publish_at' => '发布时间',
        ];
    }

    public function getOrder(){
        return $this->hasOne(OrderInfo::className(),['id'=>'own_id']);
    }

    public function getUser(){
        return $this->hasOne(UserInfo::className(),['id'=>'own_id']);
    }
}
