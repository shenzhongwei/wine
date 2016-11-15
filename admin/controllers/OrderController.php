<?php

namespace admin\controllers;

use admin\models\EmployeeInfo;
use admin\models\OrderSend;
use common\helpers\ArrayHelper;
use common\jpush\JPush;
use Yii;
use admin\models\OrderInfo;
use admin\models\OrderInfoSearch;
use yii\base\Exception;
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
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
        return $this->redirect('index');
    }

    public function actionArrive($id)
    {
        $orderinfo =$this->findModel($id);
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if($orderinfo->state==4){
                $orderinfo->state=5;
                if(!$orderinfo->save()){
                    throw new Exception('修改订单状态出错');
                }
            }else{
                throw new Exception('订单状态已变更，无法确认');
            }
            if(!empty($orderinfo->send_id)){
                $sender = EmployeeInfo::findOne($orderinfo->send_id);
                if(!empty($sender)&&$sender->status==2){
                    $sender->status=1;
                    if(!$sender->save()){
                        throw new Exception('修改配送人员状态出错');
                    }
                }
            }
            $transaction->commit();
            Yii::$app->session->setFlash('success','操作成功');
        }catch (Exception $e){
            $transaction->rollBack();
            Yii::$app->session->setFlash('danger',$e->getMessage());
        }
        return $this->redirect('index');
    }

    public function actionReceive()
    {
        $id = Yii::$app->request->get('id');
        $orderinfo =$this->findModel($id);
        if($orderinfo->state == 2){
            $orderinfo->state=3;
            if($orderinfo->save()){
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
        return $this->redirect(['view', 'id' => $orderinfo->id]);
    }

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
        $orders = OrderInfo::find()->where("$key=$value and id in $ids")->one();
        if(!empty($orders)){
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $sql = "UPDATE order_info SET $key = $valueTo";
                $sql .= " WHERE id IN $ids AND $key=$value";
                if($button == 'order_arrive'){
                    $send_ids = implode(',',array_values(array_unique(ArrayHelper::getColumn($orders,'send_id'))));
                    $sends = EmployeeInfo::find()->where("id in ($ids) and status=2");
                    if(!empty($sends)){
                        $send_sql = "UPDATE employee_info SET `status`=1 WHERE id in ($ids) AND `status`=2";
                        $sendRow = Yii::$app->db->createCommand($sql)->execute();
                        if(empty($sendRow)){
                            throw new  Exception("修改配送人员状态出错");
                        }
                    }
                }
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
//        var_dump($id);
//        exit;
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

    //接单
    public function actionPatchReceive()
    {
        $query = OrderInfo::Query();
        $query->andWhere("state=2");
        $orders = $query->all();
        if(empty($orders)){
            return $this->showResult(301,'暂无待接单状态的订单');
        }else{
            $ids = implode(',',array_values(ArrayHelper::getColumn($orders,'id')));
            $sql = "UPDATE order_info SET `state`=3 WHERE id IN ($ids) AND `state`=2";
            $row = Yii::$app->db->createCommand($sql)->execute();
            if($row){
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
            return $this->redirect(['index',"OrderInfoSearch[step]"=>3]);
        }
        if(empty($ids)){
            Yii::$app->session->setFlash('danger','未获取到您提交的订单信息');
            return $this->redirect(['index',"OrderInfoSearch[step]"=>3]);
        }
        $orders = OrderInfo::find()->where("state=3 and id in ($ids)")->all();
        if(empty($orders)){
            Yii::$app->session->setFlash('danger','您所提交的订单非待配送中的订单');
            return $this->redirect(['index',"OrderInfoSearch[step]"=>3]);
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
                $jpush = new JPush();
                $res = $jpush->pushByIdarr($userReg,'您的订单开始配送啦，点击查看',1,4);
            }
            Yii::$app->session->setFlash('success','发货成功');
            return $this->redirect(['index',"OrderInfoSearch[step]"=>4]);
        }catch (Exception $e){
            Yii::$app->session->setFlash('danger',$e->getMessage());
            return $this->redirect(['index',"OrderInfoSearch[step]"=>3]);
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
        if (($model = OrderInfo::findOne($id)) !== null) {
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
