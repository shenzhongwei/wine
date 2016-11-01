<?php

namespace admin\controllers;

use kartik\form\ActiveForm;
use Yii;
use admin\models\GoodRush;
use admin\models\RushSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * RushController implements the CRUD actions for GoodRush model.
 */
class RushController extends BaseController
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
     * Lists all GoodRush models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RushSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $dataProvider->pagination = [
            'pageSize'=>100,
        ];
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionValidForm(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $data['GoodRush']['rush_pay'] = '2|3';
        $id=Yii::$app->request->get('id');
        if(empty($id)){
            $model = new GoodRush(['scenario'=>'add']);
        }else{
            $model = new GoodRush(['scenario'=>'update']);
        }
        $model->load($data);
        return ActiveForm::validate($model);
    }



    /**
     * Creates a new GoodRush model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GoodRush;
        $post = Yii::$app->request->post();
        if($post){
            $post['GoodRush']['rush_pay'] = '2|3';
            $post['GoodRush']['start_at'] = strtotime($post['GoodRush']['start_at'].' 00:00:00');
            $post['GoodRush']['end_at'] = strtotime($post['GoodRush']['end_at'].' 23:59:59');
        }else{
            $model->rush_pay = [2,3];
        }
        if ($model->load($post) && $model->save()) {
            return $this->runAction('index');
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GoodRush model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post= Yii::$app->request->post();
        if(!empty($post)){
            $post['GoodRush']['rush_pay'] = '2|3';
            $post['GoodRush']['start_at'] = strtotime($post['GoodRush']['start_at'].' 00:00:00');
            $post['GoodRush']['end_at'] = strtotime($post['GoodRush']['end_at'].' 23:59:59');
        }
        if ($model->load($post) && $model->save()) {
            Yii::$app->session->setFlash('success','操作成功');
            return $this->runAction('index');
        } else {
            if($post){
                Yii::$app->session->setFlash('danger','操作失败');
            }else{
                $model->rush_pay = explode('|',$model->rush_pay);
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GoodRush model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = GoodRush::findOne($id);
        if($model->is_active==0){
            $model->is_active = 1;
        }else{
            $model->is_active = 0;
        }
        $model->save();
        Yii::$app->session->setFlash('success','操作成功');
        return $this->runAction('index');
    }

    public function actionPatch()
    {
        $keys = Yii::$app->request->post('keys');
        $button = Yii::$app->request->post('button');
        if(empty($keys)){
            return $this->showResult(304,'非法请求');
        }
        $ids = '('.implode(',',$keys).')';
        if($button == 'rush_up'){
            $key = 'is_active';
            $value = 0;
            $valueTo = 1;
        }elseif($button == 'rush_down'){
            $key = 'is_active';
            $value = 1;
            $valueTo = 0;
        }elseif($button == 'point_up'){
            $key = 'point_sup';
            $value = 0;
            $valueTo = 1;
        }elseif($button == 'point_down'){
            $key = 'point_sup';
            $value = 1;
            $valueTo = 0;
        }else{
            return $this->showResult(304,'非法请求');
        }
        $rushes = GoodRush::find()->where("$key=$value and id in $ids")->one();
        if(!empty($rushes)){
            $sql = "UPDATE good_rush SET $key = $valueTo";
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

    /**
     * Finds the GoodRush model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GoodRush the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GoodRush::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
