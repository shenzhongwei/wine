<?php
namespace api\controllers;
use api\models\CommentDetail;
use api\models\GoodInfo;
use api\models\OrderComment;
use api\models\OrderDetail;
use api\models\OrderInfo;
use Yii;
use yii\base\Exception;

/**
 * Created by PhpStorm.
 * User: szw
 * Date: 2016/8/24
 * Time: 14:21
 */

class CommentController extends  ApiController{
    /**
     * 商品评价列表
     */
    public function actionCommentList(){
        //获取商品id
        $good_id = Yii::$app->request->post('good_id');
        //获取页数
        $page = Yii::$app->request->post('page',1);
        $pageSize = Yii::$app->params['pageSize'];
        if(empty($good_id)){
            return $this->showResult(301,'获取数据异常');
        }
        //判断产品信息是否存在
        $goodInfo = GoodInfo::findOne(['id'=>$good_id,'is_active'=>1]);
        if(empty($goodInfo)){
            return $this->showResult(303,'未获取到产品信息');
        }
        //查找商品的评论//计算数量//分页
        $query = CommentDetail::find()->joinWith('c')->where(['gid'=>$good_id,'comment_detail.status'=>1])->orderBy(['order_comment.add_at'=>SORT_DESC]);
        $count = $query->count();
        $query->offset(($page-1)*$pageSize)->limit($pageSize);
        $comments = $query->all();
        //处理数据
        $data = CommentDetail::data($comments);
        return $this->showList(200,'成功',$count,$data);
    }


    /**
     * 评价订单接口
     */
    public function actionOrderComment(){
        $user_id = Yii::$app->user->identity->getId();
        $order_id = Yii::$app->request->post('order_id');//获取订单id
        if(empty($order_id)){//判断是否获取到id
            return $this->showResult(301,'读取订单信息失败');
        }
        $send_star = Yii::$app->request->post('send_star',0);//送货评分
        //good_comment:[{"good_id":3,"good_star":3,"content":100.00},{"good_id":3,"good_star":3,"content":100.00}]
        $good_comment = json_decode(stripcslashes(Yii::$app->request->post('good_comment')),true);//商品评价
//        $str = stripcslashes(Yii::$app->request->post('good_comment'));
        if(empty($good_comment)){
            return $this->showResult(301,'获取评价内容失败');
        }
        $userOrder = OrderInfo::findOne(['uid'=>$user_id,'id'=>$order_id,'state'=>6]);//查找订单
        if(empty($userOrder)){//判断订单状态
            return $this->showResult(304,'订单数据异常，请重试');
        }
        //判断订单是否已评价
        $commentOrder = OrderComment::findOne(['uid'=>$user_id,'oid'=>$order_id]);
        if(!empty($commentOrder)){
            $userOrder->state = 7;
            if($userOrder->save()){
                return $this->showResult(303,'该订单已评价');
            }else{
                return $this->showResult(304,'订单数据异常，请重试');
            }
        }
        //判断商品id是否为该订单的
        foreach($good_comment as $value){
            $orderDetail = OrderDetail::findOne(['oid'=>$order_id,'gid'=>$value['good_id']]);
            if(empty($orderDetail)){
                return $this->showResult(304,'订单数据异常，请重试');
            }
        }
        //开启事务存入数据库
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $orderComment = new OrderComment();
            $userOrder->state = 7;
            if(!$userOrder->save()){
                throw new Exception('修改订单状态失败');
            }
            $orderComment->attributes = [
                'oid'=>$order_id,
                'uid'=>$user_id,
                'add_at'=>time(),
                'send_star'=>$send_star,
                'status'=>1
            ];
            if(!$orderComment->save()){
                throw new Exception('保存订单评论失败');
            }
            foreach($good_comment as $value){
                $commentDetail = new CommentDetail();
                $commentDetail->attributes = [
                    'cid'=>$user_id,
                    'gid'=>$value['good_id'],
                    'star'=>$value['good_star'],
                    'content'=>$value['content'],
                    'status'=>1
                ];
                if(!$commentDetail->save()){
                    throw new Exception('保存评论详情失败');
                }
            }
            $transaction->commit();
            return $this->showResult(200,'评价成功');
        }catch (Exception $e){
            $transaction->rollBack();
            return $this->showResult(400,$e->getMessage());
        }
    }
}