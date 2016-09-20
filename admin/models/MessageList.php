<?php

namespace admin\models;

use Yii;
use yii\helpers\ArrayHelper;

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
            [['publish_at'], 'safe'],
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
            'id' => 'ID',
            'type_id' => '消息类型',
            'title' => '消息标题',
            'content' => '消息内容',
            'own_id' => '所属人',
            'target' => '消息跳转页面',
            'status' => '状态（已读、未读）',
            'publish_at' => '发布时间',
        ];
    }

    /*
     *根据消息类型获取对应人集合
     */
    public static function getacceptName($type){
        switch($type){
            case 1: //系统消息
                $model=array(
                    array(
                        "id"=>"2",
                        "name"=>"系统管理员",
                    )
                );
                break;
            case 2: //用户消息
                $model=\admin\models\UserInfo::find()->select(['id','phone as name'])->asArray()->all();
                break;
            case 3: //订单消息
                $model=\admin\models\OrderInfo::find()->select(['id','order_code as name'])->asArray()->all();;
                break;
            case 4: //商品通知
                $model=\admin\models\GoodInfo::find()->select(['id','name'])->asArray()->all();;
                break;
            default:
                $model=[[]];
                break;
        }
        $results=[];
        if(!empty($model)){
            $results = ArrayHelper::map($model,'id','name');
        }
        return $results;
    }
}
