<?php
namespace api\controllers;

/**
 * Created by PhpStorm.
 * User: me
 * Date: 2016/8/29
 * Time: 14:38
 */

use api\models\UserTicket;
use common\helpers\ArrayHelper;
use Yii;

class TicketController extends ApiController{

    /**
     * 优惠券列表接口
     */
    public function actionList(){
        $user_id = Yii::$app->user->identity->getId();
        $page = Yii::$app->request->post('page',1);//页数
        $type = Yii::$app->request->post('type',1);//类型，优惠券列表1 下单页面选择优惠券2
        if(UserTicket::AutoOverTimeTicket($user_id)){
            $pageSize = Yii::$app->params['pageSize'];
            $query = UserTicket::find()->joinWith('p')->where(['uid'=>$user_id]);
            if($type==1){
                $count = $query->count();
            }elseif($type==2){
                $query->andWhere(['status'=>1]);
                $count = $query->count();
            }else{
                return $this->showResult(301,'获取数据异常');
            }
            $query->offset(($page-1)*$pageSize)->limit($pageSize);
            $userTickets = $query->all();
            $data = ArrayHelper::getColumn($userTickets,function($element){
                return [
                    'ticket_id'=>$element->id,
                    'discount'=>$element->p->discount,
                    'name'=>$element->p->name,
                    'condition'=>$element->p->condition,
                    'valid_date'=>($element->start_at==0 && $element->end_at==0) ? '永久有效':date('Y-m-d',$element->start_at).'至'.date('Y-m-d',$element->end_at)
                ];
            });
            return $this->showList(200,'优惠券列表如下',$count,$data);
        }else{
            return $this->showResult(400,'服务器异常');
        }
    }
}