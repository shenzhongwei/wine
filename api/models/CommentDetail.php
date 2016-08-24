<?php

namespace api\models;

use Yii;
use yii\helpers\ArrayHelper;

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
 * @property OrderComment $c
 * @property GoodInfo $g
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
            [['cid'], 'exist', 'skipOnError' => true, 'targetClass' => OrderComment::className(), 'targetAttribute' => ['cid' => 'id']],
            [['gid'], 'exist', 'skipOnError' => true, 'targetClass' => GoodInfo::className(), 'targetAttribute' => ['gid' => 'id']],
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
    public function getC()
    {
        return $this->hasOne(OrderComment::className(), ['id' => 'cid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getG()
    {
        return $this->hasOne(GoodInfo::className(), ['id' => 'gid']);
    }


    /**
     * @param array $arr
     * @return array
     * 处理model
     */
    public static function data($arr=[]){
        $res = ArrayHelper::getColumn($arr,function($element){
            if(empty($element->c)||empty($element->c->u)){
                $nickName = '用户已失效';
                $phone = '用户已失效';
            }else{
                $nickName = $element->c->u->nickname;
                $phone = substr($element->c->u->phone,0,7).'****';
            }
            return [
                'user'=>$nickName,
                'phone'=>$phone,
                'comment_at'=>empty($element->c) ? '失效':date('Y-m-d',$element->c->add_at),
                'content'=>$element->content,
            ];
        });
        return $res;
    }
}
