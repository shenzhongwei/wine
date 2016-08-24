<?php
namespace api\controllers;
use api\models\CommentDetail;
use api\models\GoodInfo;
use Yii;

/**
 * Created by PhpStorm.
 * User: me
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
}