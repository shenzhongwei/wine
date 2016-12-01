<?php

namespace admin\controllers;

use admin\models\EmployeeInfo;
use admin\models\OrderSend;
use common\helpers\ArrayHelper;
use common\JPush\PushModel;
use Yii;
use admin\models\OrderInfo;
use admin\models\OrderInfoSearch;
use yii\base\Exception;
use yii\base\Object;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for OrderInfo model.
 */
class OrderController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post','get'],
                ],
            ],
        ];
    }

    /**
     * Lists all OrderInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderInfoSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $dataProvider->pagination=[
                'pageSize' => 15,
        ];
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    /**
     * Displays a single OrderInfo model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', ['model' => $model]);
    }


    public function actionLocate()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        if (empty($model) || empty($model->a)) {
            $address = [];
            $distance = 0;
        } else {
            $address = $model->a;
            $distance = $model->distance;
        }
        return $this->renderAjax('locate', ['model' => $address,'distance'=>$distance]);
    }





    /**
     * Deletes an existing OrderInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $orderinfo =$this->findModel($id);
        if($orderinfo->status == 0){
            $orderinfo->status=1;
        }else{
            $orderinfo->status=0;
        }
        if($orderinfo->save()){
            Yii::$app->session->setFlash('success','操作成功');
        }else{
            Yii::$app->session->setFlash('danger','操作失败');
        }
        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * 确认送达api
     */
    public function actionArrive($id)
    {
        $orderinfo =$this->findModel($id);
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if($orderinfo->state==4){
                $orderinfo->state=5;
                $orderinfo->order_date = time();
                if(!$orderinfo->save()){
                    throw new Exception('修改订单状态出错');
                }
            }else{
                throw new Exception('订单状态已变更，无法确认送达');
            }
            if(!empty($orderinfo->send_id)){
                $sender = EmployeeInfo::findOne(['id'=>$orderinfo->send_id,'status'=>2]);
                if(!empty($sender)){
                    //判断有没有其他订单代配送
                    $sendingOrder = OrderInfo::find()->where("id<>$id and send_id=$sender->id and state=4")->all();
                    if(empty($sendingOrder)){
                        $sender->status=1;
                        if(!$sender->save()){
                            throw new Exception('修改配送人员状态出错');
                        }
                    }
                }
            }
            $transaction->commit();
            Yii::$app->session->setFlash('success','操作成功');
        }catch (Exception $e){
            $transaction->rollBack();
            Yii::$app->session->setFlash('danger',$e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * @return \yii\web\Response
     * 接单api
     */
    public function actionReceive()
    {
        $id = Yii::$app->request->get('id');
        $orderinfo =$this->findModel($id);
        if($orderinfo->state == 2){
            $orderinfo->state=3;
            if($orderinfo->save()){
                $userInfo = $orderinfo->u;
                if(!empty($userInfo)&&!empty($userInfo->userLogin->reg_id)){
                    $message = '店铺已经开始处理您的订单啦，点击查看';
                    $target = 4;
                    $title = '店铺接单';
                    $extra = ['target'=>$target];
                    $jpush = new PushModel();
                    $result = @$jpush->PushReg($message,$userInfo->userLogin->reg_id,$title,$extra,$title);
                }
                Yii::$app->session->setFlash('success','接单成功');
            }else{
                Yii::$app->session->setFlash('danger','操作失败');
            }
        }elseif($orderinfo->state==1){
            Yii::$app->session->setFlash('danger','订单未付款，无法接单');
        }elseif ($orderinfo->state==99){
            Yii::$app->session->setFlash('danger','订单已取消，无法接单');
        }elseif ($orderinfo->state==100){
            Yii::$app->session->setFlash('danger','订单已退款，无法接单');
        }else{
            Yii::$app->session->setFlash('danger','订单状态已更新，无法接单');
        }
        return $this->redirect(['index']);
    }

    /**
     * @return array
     * 批量处理pai
     */
    public function actionPatch()
    {
        $keys = Yii::$app->request->post('keys');
        $button = Yii::$app->request->post('button');
        if(empty($keys)){
            return $this->showResult(304,'非法请求');
        }
        $ids = '('.implode(',',$keys).')';
        if($button == 'order_delete'){
            $key = 'status';
            $value = 1;
            $valueTo = 0;
        }elseif($button == 'order_recover'){
            $key = 'status';
            $value = 0;
            $valueTo = 1;
        }elseif($button == 'order_arrive'){
            $key = 'state';
            $value = 4;
            $valueTo = 5;
        }else{
            return $this->showResult(304,'非法请求');
        }
        $orders = OrderInfo::find()->where("$key=$value and id in $ids")->all();
        if(!empty($orders)){
            $transaction = Yii::$app->db->beginTransaction();
            try{
                //订单的sql
                $sql = "UPDATE order_info SET $key = $valueTo";
                //批量送达的sql
                if($button == 'order_arrive'){
                    $sql.=",order_date=".time();
                    $sendArr = array_values(array_unique(ArrayHelper::getColumn($orders,'send_id')));
                    $send_ids = implode(',',$sendArr);
                    $sendingOrders = OrderInfo::find()->where("send_id in ($send_ids) and state=4 and id not in $ids")->all();
                    if(!empty($sendingOrders)){
                        $sendingArr = array_values(array_unique(ArrayHelper::getColumn($orders,'send_id')));
                        foreach ($sendingArr as $index=>$value){
                            if(in_array($value,$sendArr)){
                                unset($sendArr[$index]);
                            }
                        }
                        $send_ids = implode(',',$sendArr);
                    }
                    if(!empty($send_ids)){
                        $sends = EmployeeInfo::find()->where("id in ($send_ids)  AND `status`=2")->all();
                        if(!empty($sends)){
                            $send_sql = "UPDATE employee_info SET `status`=1 WHERE id in ($send_ids) AND `status`=2";
                            $sendRow = Yii::$app->db->createCommand($send_sql)->execute();
                            if(empty($sendRow)){
                                throw new  Exception("修改配送人员状态出错");
                            }
                        }
                    }
                }
                $sql .= " WHERE id IN $ids AND $key=$value";
                $res = Yii::$app->db->createCommand($sql)->execute();
                if(empty($res)){
                    throw new  Exception("订单状态出错");
                }
                $transaction->commit();
                return $this->showResult(200,'操作成功');
            }catch (Exception $e){
                $transaction->rollBack();
                return $this->showResult(400,$e->getMessage());
            }
        }else{
            return $this->showResult(303,'您选择的订单无法执行该操作');
        }
    }

    public function actionSend(){
        $key = Yii::$app->request->post('key');
        $id = Yii::$app->request->post('id');
        $model = new OrderSend();
        if($key=='single'){
            $model->id_str = $id;
        }elseif ($key=='patch'){
            $model->id_str = implode(',',$id);
        }else{
            $model->id_str = '';
        }
        return $this->renderAjax('send',['model'=>$model]);
    }

    //一键接单
    public function actionPatchReceive()
    {
        $query = OrderInfo::Query();
        $query->andWhere("`state`=2");
        $query->joinWith('u')->andWhere('user_info.id>0');
        $orders = $query->all();
        if(empty($orders)){
            return $this->showResult(301,'暂无待接单状态的订单');
        }else{
            $ids = implode(',',array_values(ArrayHelper::getColumn($orders,'id')));
            $sql = "UPDATE order_info SET `state`=3 WHERE id IN ($ids) AND `state`=2";
            $row = Yii::$app->db->createCommand($sql)->execute();
            if($row){
                $regArr = array_filter(ArrayHelper::getColumn($orders,function($element){
                    return $element->u->userLogin->reg_id;
                }));
                if(!empty($regArr)){
                    $message = '店铺已经开始处理您的订单啦，点击查看';
                    $target = 4;
                    $title = '店铺接单';
                    $extra = ['target'=>$target];
                    $jpush = new PushModel();
                    $result = @$jpush->PushReg($message,$regArr,$title,$extra,$title);
                }
                return $this->showResult(200,'接单成功');
            }else{
                return $this->showResult(400,'接单失败，稍后再试');
            }
        }
    }

    //配送订单
    public function actionSendOrder(){
        $post = Yii::$app->request->post('OrderSend');
        $send_id = $post['send_id'];
        $ids = $post['id_str'];
        $send_now = $post['send_now'];
        $employee = EmployeeInfo::findOne($send_id);
        if($employee->status<>1){
            if($employee->status == 0){
                Yii::$app->session->setFlash('danger','该人员已被删除');
            }elseif($employee->status == 2){
                Yii::$app->session->setFlash('danger','该人员已在配送商品中，无法派单');
            }elseif($employee->status == 3){
                Yii::$app->session->setFlash('danger','该人员已下岗');
            }
            return $this->redirect(['index']);
        }
        if(empty($ids)){
            Yii::$app->session->setFlash('danger','未获取到您提交的订单信息');
            return $this->redirect(['index']);
        }
        $orders = OrderInfo::find()->where("state=3 and id in ($ids)")->all();
        if(empty($orders)){
            Yii::$app->session->setFlash('danger','您所提交的订单非待配送中的订单');
            return $this->redirect(['index']);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $send_code = $this->gen_trade_no();
            $sql = "UPDATE order_info SET send_id = $send_id, state=4,send_code=CONCAT('$send_code',id) WHERE id in ($ids) AND state=3";
            $rows = Yii::$app->db->createCommand($sql)->execute();
            if(empty($rows)){
                throw new Exception('修改订单出错');
            }
            $employee->status = $send_now;
            if(!$employee->save()){
                throw new Exception('修改配送人员状态出错');
            }
            $userReg = [];
            $message_sql = "INSERT INTO message_list VALUES ";
            foreach ($orders as $order){
                $message_sql.="(NULL,3,'订单配送中','订单编号为$order->order_code 的订单正在配送中',$order->id,3,0,'".date('Y-m-d')."'),";
                if(!empty($order->u)&&!empty($order->u->userLogin)&&!empty($order->u->userLogin->reg_id)){
                    $userReg[]=$order->u->userLogin->reg_id;
                }
            }
            $messageRows = $rows = Yii::$app->db->createCommand(rtrim($message_sql,','))->execute();
            if(empty($messageRows)){
                throw new Exception('保存订单消息出错');
            }
            $transaction->commit();
            if(!empty($userReg)){
                $message = '您的订单开始配送啦，点击查看';
                $target = 4;
                $title = '订单配送啦';
                $extra = ['target'=>$target];
                $jpush = new PushModel();
                $result = $jpush->PushReg($message,$userReg,$title,$extra,$title);
            }
            Yii::$app->session->setFlash('success','发货成功');
            return $this->redirect(['index']);
        }catch (Exception $e){
            Yii::$app->session->setFlash('danger',$e->getMessage());
            return $this->redirect(['index']);
        }
    }
    /**
     * Finds the OrderInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = OrderInfo::find()->joinWith(['a','s'])->addSelect([
            'order_info.*',
            "ROUND(6378.138*2*ASIN(SQRT(POW(SIN((shop_info.lat/1000000*PI()/180-user_address.lat/1000000*PI()/180)/2),2)+COS(shop_info.lat/1000000*PI()/180)*COS(user_address.lat/1000000*PI()/180)*POW(SIN((shop_info.lng/1000000*PI()/180-user_address.lng/1000000*PI()/180)/2),2)))*1000) as distance"
        ])->where("order_info.id=$id")->one();
        if ($model!== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



    /*获取物流编号*/
    public function gen_trade_no(){
        $str = 'abcdefghijklmnopqrstuvwxyz';
        $length = strlen($str) - 1;
        $strs = $str[rand(0,$length)].$str[rand(0,$length)];
        return strtoupper($strs.date('MdHis'));
    }
}
