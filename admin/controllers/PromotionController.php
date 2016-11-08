<?php

namespace admin\controllers;

use admin\models\Dics;
use admin\models\PromotionType;
use Yii;
use admin\models\PromotionInfo;
use admin\models\PromotionInfoSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use admin\models\MerchantInfo;
use admin\models\ShopInfo;
use admin\models\GoodInfo;
/**
 * PromotionController implements the CRUD actions for PromotionInfo model.
 */
class PromotionController extends BaseController
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

    public function actionIndex()
    {
        $searchModel = new PromotionInfoSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $dataProvider->pagination = [
            'pageSize'=>15,
        ];
        $dataProvider->sort = [
            'defaultOrder'=>['valid_circle'=>SORT_ASC,'is_active'=>SORT_DESC,]
        ];

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }


    public function actionCreate()
    {
        $model = new PromotionInfo;

        if (Yii::$app->request->post()) {
            $promotion=Yii::$app->request->post('PromotionInfo');
            $model->attributes=$promotion;
            $model->regist_at=time();
            $model->active_at=time();

            if($promotion['valid_circle']==-1){
                $start_at=strtotime($promotion['start_from']);
                $end_at=strtotime($promotion['end_to']);
                $model->valid_circle=($end_at-$start_at)/24/3600;
                $model->start_at=$start_at;
                $model->end_at=$end_at;
            }

            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->post()) {
            $promotion=Yii::$app->request->post('PromotionInfo');
            $model->attributes=$promotion;

            if($promotion['valid_circle']==-1){
                $start_at=strtotime($promotion['start_from']);
                $end_at=strtotime($promotion['end_to']);
                $model->valid_circle=($end_at-$start_at)/24/3600;
                $model->start_at=$start_at;
                $model->end_at=$end_at;
            }

            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $model->start_from=date('Y-m-d',$model->start_at);
            $model->end_to=date('Y-m-d',$model->end_at);

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    public function actionDelete()
    {
        $user_id = Yii::$app->user->identity->getId();
        if(empty($user_id)){
            return $this->showResult(302,'用户登录信息失效');
        }
        $id=Yii::$app->request->get('id');
        if(empty($id)){
            return $this->showResult(301,'读取数据发生错误');
        }
        $query =PromotionInfo::findOne([$id]);
        if(empty($query)){
            return $this->showResult(301,'未获取到该活动的信息');
        }

        if($query->is_active==1){
            $query->is_active=0;
        }else{
            $query->is_active=1;
        }
        $query->active_at=time();
        $query->save();
        return $this->redirect(['index']);

    }


    protected function findModel($id)
    {
        if (($model = PromotionInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionLimit(){
        $depDrop = Yii::$app->request->post('depdrop_parents');

        $res = [];
        if (isset($depDrop)) {
            $id = end($depDrop);
            $type = PromotionType::findOne($id);
            if(!empty($type)){
                if(in_array($type->group,[1,4,5])){
                    $results = Dics::getPromotionRange(0);
                    foreach ($results as $key=>$value){
                        $res[]=[
                            'id'=>$key,
                            'name'=>$value,
                        ];
                    }
                }else{
                    $res = [[
                        'id'=>1,
                        'name'=>'平台通用',]
                    ];
            }
            }
        }
        echo Json::encode(['output' => empty($res) ? '':$res, 'selected'=>'']);
        exit;
    }

    public function actionStyles(){
        $depDrop = Yii::$app->request->post('depdrop_parents');

        $res = [];
        if (isset($depDrop)) {
            $id = end($depDrop);
            $type = PromotionType::findOne($id);
            if(!empty($type)){
                $results = PromotionInfo::GetStyles($type->id);
                foreach ($results as $key=>$value){
                    $res[]=[
                        'id'=>$key,
                        'name'=>$value,
                    ];
                }
            }
        }
        echo Json::encode(['output' => empty($res) ? '':$res, 'selected'=>'']);
        exit;
    }


    public function actionTargets(){
        $depDrop = Yii::$app->request->post('depdrop_parents');

        $res = [];
        if (isset($depDrop)) {
            $id = end($depDrop);
            $results = PromotionInfo::GetTargets($id);
        }
        if(!empty($results)){
            foreach ($results as $key=>$value){
                $res[]=[
                    'id'=>$key,
                    'name'=>$value,
                ];
            }
        }
        echo Json::encode(['output' => empty($res) ? '':$res, 'selected'=>'']);
        exit;
    }

    public function actionStyleChange(){
        $user_id = Yii::$app->user->identity;
        if(empty($user_id)){
            return $this->showResult(302,'读取用户信息出错');
        }
        $type = Yii::$app->request->post('type');
        if(empty($promotionType)){
            return $this->showResult(301,'服务器异常');
        }
        $promotionType = PromotionType::findOne(['name'=>$type]);
        if($promotionType->group == 3){
            $res = 1;
        }else{
            $res = 0;
        }
        return $this->showResult(200,'成功',$res);
    }

    public function actionTypeChange(){
        $user_id = Yii::$app->user->identity;
        if(empty($user_id)){
            return $this->showResult(302,'读取用户信息出错');
        }
        $type = Yii::$app->request->post('type');
        $promotionType = PromotionType::findOne($type);
        if(empty($promotionType)){
            return $this->showResult(301,'服务器异常');
        }
        if($promotionType->group == 1){
            //是优惠券的形式则可操作
            $is_ticket = 1;
        }else{
            //费优惠券不可操作
            $is_ticket = 0;
        }
        if($promotionType->group==3||$promotionType->env==1){
            //会员特权和用户注册的活动次数限制一次
            $is_time = 0;
        }elseif ($promotionType->group==5||$promotionType->env == 3){
            //分享网页与下单时无限制
            $is_time = 2;
        }else{
            //其他可操作
            $is_time = 1;
        }
        $data = [
            'is_ticket'=>$is_ticket,
            'is_time'=>$is_time,
        ];
        return $this->showResult(200,'成功',$data);
    }
}
