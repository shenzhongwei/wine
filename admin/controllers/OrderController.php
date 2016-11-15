<?php

namespace admin\controllers;

use admin\models\OrderSend;
use common\helpers\ArrayHelper;
use Yii;
use admin\models\OrderInfo;
use admin\models\OrderInfoSearch;
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
        }else{
            return $this->showResult(304,'非法请求');
        }
        $orders = OrderInfo::find()->where("$key=$value and id in $ids")->one();
        if(!empty($orders)){
            $sql = "UPDATE order_info SET $key = $valueTo";
            $sql .= " WHERE id IN $ids AND $key=$value";
            $res = Yii::$app->db->createCommand($sql)->execute();
            if(!empty($res)){
                return $this->showResult(200,'操作成功');
            }else{
                return $this->showResult(400,'操作失败，请稍后重试');
            }
        }else{
            return $this->showResult(200,'操作成功');
        }
    }

    public function actionSend(){
        $key = Yii::$app->request->post('key');
        $id = Yii::$app->request->post('id');
        $model = new OrderSend();
        if($key='single'){
            $model->id_str = $id;
        }elseif ($key='patch'){
            $model->id_str = implode(',',$id);
        }else{
            $model->id_str = '';
        }
        return $this->render('send',['model'=>$model]);
    }

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
    public function gen_trade_no($id){
        $str = 'abcdefghijklmnopqrstuvwxyz';
        $length = strlen($str) - 1;
        $strs = $str[rand(0,$length)].$str[rand(0,$length)];
        return $strs.date('His').$id;
    }
}
