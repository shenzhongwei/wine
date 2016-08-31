<?php

namespace api\controllers;
/**
 * Created by PhpStorm.
 * User: szw
 * Date: 2016/8/30
 * Time: 15:14
 */
use api\models\MessageList;
use api\models\UserInfo;
use Yii;
use yii\helpers\ArrayHelper;

class MessageController extends ApiController{

    /**
     * 个人中心页面有无未读消息api
     */
    public function actionUnreadMessage(){
        $user_id = Yii::$app->user->identity->getId();
        //判断
        $count = MessageList::find()->joinWith('user')->joinWith('order')->where(
            "((own_id=$user_id and type_id=2 and user_info.id>0) or (uid=$user_id and type_id=3 and order_info.id>0) )and message_list.status=0")->count();
        return $this->showResult(200,'成功',$count);
    }

    /**
     *我的消息列表接口
     */
    public function actionMessageList(){
        $user_id = Yii::$app->user->identity->uid;
        $page = Yii::$app->request->post('page',1);
        $pageSize = Yii::$app->params['pageSize'];
        $userInfo = UserInfo::findOne($user_id);
        if(empty($userInfo)){
            return $this->showResult(302,'用户信息异常');
        }
        //查找 用户，订单 系统和商户
        $query = MessageList::find()->joinWith('user')->joinWith('order');
        $query->where("
        ((own_id=$user_id and type_id=2 and user_info.id>0) or
        (uid=$user_id and type_id=3 and order_info.id>0)) or
        (type_id in (1,4) and unix_timestamp(publish_at)+86400>=".strtotime($userInfo->created_time).")");
        $query->orderBy(['publish_at'=>SORT_DESC]);
        $count = $query->count();
        $query->offset(($page-1)*$pageSize)->limit($pageSize);
        $messages = $query->all();
        $data = ArrayHelper::getColumn($messages,function($element){
            return [
                'message_id'=>$element->id,
                'type'=>$element->type_id,
                'target'=>$element->target,
                'param'=>$element->own_id,
                'title'=>$element->title,
                'content'=>$element->content,
                'is_read'=>$element->status,
                'publish_at'=>$element->publish_at
            ];
        });
        return $this->showList(200,'列表如下',$count,$data);
    }


    /**
     * 点击消息变为已读api
     */
    public function actionReadMessage(){
        $user_id = Yii::$app->user->identity->getId();
        $message_id = Yii::$app->request->post('message_id');//获取消息id
        if(empty($message_id)){
            return $this->showResult(301,'获取消息数据出错');
        }
        $messageInfo = MessageList::findOne($message_id);
        $messageInfo->status=1;
        if($messageInfo->save()){
            return $this->showResult(200,'成功');
        }else{
            return $this->showResult(400,'失败');
        }
    }

}