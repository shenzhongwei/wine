<?php

namespace admin\controllers;

use admin\models\Dics;
use admin\models\PromotionType;
use kartik\form\ActiveForm;
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
use yii\web\Response;

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

    public function actionValidForm(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $id=Yii::$app->request->get('id');
        if(!empty($data['PromotionInfo']['start_at'])){
            $data['PromotionInfo']['start_at'] = strtotime($data['PromotionInfo']['start_at'].' 00:00:00');
        }
        if(!empty($data['PromotionInfo']['end_at'])){
            $data['PromotionInfo']['end_at'] = strtotime($data['PromotionInfo']['end_at'].' 23:59:59');
        }
        if(!empty($data['PromotionInfo']['pt_id'])){
            $promotionType = PromotionType::findOne($data['PromotionInfo']['pt_id']);
            if($promotionType->group==3||$promotionType->env==1){
                //会员特权和用户注册的活动次数限制一次
                $data['PromotionInfo']['time_valid']=1;
                $data['PromotionInfo']['time']=1;
            }elseif ($promotionType->group==5||$promotionType->env == 3){
                //分享网页与下单时无限制
                $data['PromotionInfo']['time_valid']=0;
                $data['PromotionInfo']['time']=0;
            }
        }
        $model = new PromotionInfo();
        if(!empty($id)){
            $model->id=$id;
        }
        $model->load($data);
        return ActiveForm::validate($model);
    }


    public function actionCreate()
    {
        $model = new PromotionInfo;
        $data = Yii::$app->request->post();
        if(!empty($data['PromotionInfo']['start_at'])){
            $data['PromotionInfo']['start_at'] = strtotime($data['PromotionInfo']['start_at'].' 00:00:00');
        }
        if(!empty($data['PromotionInfo']['end_at'])){
            $data['PromotionInfo']['end_at'] = strtotime($data['PromotionInfo']['end_at'].' 23:59:59');
        }
        if($data){
            $promotionType = PromotionType::findOne($data['PromotionInfo']['pt_id']);
            if($promotionType->group==3||$promotionType->env==1){
                //会员特权和用户注册的活动次数限制一次
                $data['PromotionInfo']['time_valid']=1;
                $data['PromotionInfo']['time']=1;
            }elseif ($promotionType->group==5||$promotionType->env == 3){
                //分享网页与下单时无限制
                $data['PromotionInfo']['time_valid']=0;
                $data['PromotionInfo']['time']=0;
            }
        }
        if ($model->load($data)) {
            if($model->date_valid==0){
                $model->start_at=0;
                $model->end_at = 0;
            }
            if($model->time_valid==0){
                $model->time=0;
            }
            if($model->circle_valid==0){
                $model->valid_circle = 0;
            }
            if(empty($model->condition)){
                $model->condition = 0;
            }
            $model->regist_at=time();
            $model->active_at=time();
            $model->is_active=1;
            if( $model->save()){
                Yii::$app->session->setFlash('success','操作成功');
                return $this->redirect(['index']);
            }else{
                Yii::$app->session->setFlash('danger','操作失败');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $data = Yii::$app->request->post();
        if(!empty($data['PromotionInfo']['start_at'])){
            $data['PromotionInfo']['start_at'] = strtotime($data['PromotionInfo']['start_at'].' 00:00:00');
        }
        if(!empty($data['PromotionInfo']['end_at'])){
            $data['PromotionInfo']['end_at'] = strtotime($data['PromotionInfo']['end_at'].' 23:59:59');
        }
        if($data){
            $promotionType = PromotionType::findOne($data['PromotionInfo']['pt_id']);
            if($promotionType->group==3||$promotionType->env==1){
                //会员特权和用户注册的活动次数限制一次
                $data['PromotionInfo']['time_valid']=1;
                $data['PromotionInfo']['time']=1;
            }elseif ($promotionType->group==5||$promotionType->env == 3){
                //分享网页与下单时无限制
                $data['PromotionInfo']['time_valid']=0;
                $data['PromotionInfo']['time']=0;
            }
        }
        if ($model->load($data)) {
            if($model->date_valid==0){
                $model->start_at=0;
                $model->end_at = 0;
            }
            if($model->time_valid==0){
                $model->time=0;
            }
            if($model->circle_valid==0){
                $model->valid_circle = 0;
            }
            if(empty($model->condition)){
                $model->condition = 0;
            }
            if( $model->save()){
                Yii::$app->session->setFlash('success','操作成功');
                return $this->redirect(['index']);
            }else{
                Yii::$app->session->setFlash('danger','操作失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            if($model->end_at>0){
                $model->date_valid=1;
            }else{
                $model->date_valid=0;
            }
            if($model->time>0){
                $model->time_valid=1;
            }else{
                $model->time_valid=0;
            }
            if($model->valid_circle>0){
                $model->circle_valid=1;
            }else{
                $model->circle_valid=0;
            }
//            var_dump($is_time);
//            exit;
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->is_active==1){
            $model->is_active=0;
        }else{
            $model->is_active=1;
        }
        $model->date_valid = $model->end_at>0 ? 1:0;
        $model->time_valid = $model->time>0 ? 1:0;
        $model->active_at=time();
        $model->save();
        Yii::$app->session->setFlash('success','操作成功');
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
        $promotionType = PromotionType::findOne(['name'=>$type]);
        if(empty($promotionType)){
            return $this->showResult(301,'服务器异常');
        }
        if($promotionType->group == 3){
            $res = 1;
        }elseif ($promotionType->env==2 && $promotionType->group==2){
            $res = 2;
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

    public function actionPromotion(){
        $user_id = Yii::$app->user->identity;
        if(empty($user_id)){
            return $this->showResult(302,'读取用户信息出错');
        }
        $id = Yii::$app->request->post('id');
        $promotion = PromotionInfo::findOne($id);
        if(empty($promotion)){
            return $this->showResult(301,'服务器异常');
        }
        $promotionType = $promotion->pt;
        if(empty($promotion)){
            return $this->showResult(301,'促销种类不存在');
        }
        //优惠与条件
        if($promotionType->group==1&&$promotion->style=1){
            $is_condition = 1;
            $condition_value = $promotion->condition;
            $is_discount = 1;
            $discount_value = $promotion->discount;
            $condition_placeholder = '输入优惠条件';
            $discount_placeholder = '输入优惠额度';
        }elseif($promotionType->group==3&&$promotion->style=1){
            $is_condition = 1;
            $condition_value = $promotion->condition;
            $is_discount = 0;
            $discount_value = '';
            $condition_placeholder = '输入优惠条件';
            $discount_placeholder = '该种类无需输入优惠额度';
        }elseif ($promotionType->env==2&&$promotionType->group==2&&$promotion->style=1){
            $is_condition = 0;
            $condition_value = '';
            $is_discount = 1;
            $discount_value = $promotion->discount;
            $condition_placeholder = '该种类下无需输入条件';
            $discount_placeholder = '输入优惠额度';
        }else{
            if($promotion->style==1){
                $is_condition = 1;
                $condition_value = $promotion->condition;
                $is_discount = 1;
                $discount_value = $promotion->discount;
                $condition_placeholder = '输入优惠条件';
                $discount_placeholder = '输入优惠额度';
            }else{
                $is_condition = 0;
                $condition_value = '';
                $is_discount = 1;
                $discount_value = $promotion->discount;
                $condition_placeholder = '该优惠形式无需输入条件';
                $discount_placeholder = '输入优惠百分比';
            }
        }
        if($promotionType->group == 1){
            //是优惠券的形式则可操作
            $is_ticket = 1;
            $ticket_check = $promotion->valid_circle>0 ? 1:0;
            $ticket_value = $promotion->valid_circle>0 ? $promotion->valid_circle:'';
            $ticket_disable = $promotion->valid_circle>0 ? 0:1;
            $ticket_placeholder = $promotion->valid_circle>0 ? '输入优惠券有效期(单位：天)':'该形式无需输入优惠券的有效期';
        }else{
            //费优惠券不可操作
            $is_ticket = 0;
            $ticket_check = 0;
            $ticket_value = '';
            $ticket_disable = 1;
            $ticket_placeholder = '该形式无需输入优惠券的有效期';
        }
        if($promotionType->group==3||$promotionType->env==1){
            //会员特权和用户注册的活动次数限制一次
            $is_time = 0;
            $time_check =  1;
            $time_value = 1;
            $time_disable = 1;
            $time_placeholder = '请输入可参与次数';
        }elseif ($promotionType->group==5||$promotionType->env == 3){
            //分享网页与下单时无限制
            $is_time = 0;
            $time_check =  0;
            $time_value = '';
            $time_disable = 1;
            $time_placeholder = '该形式无需输入优惠券的有效期';
        }else{
            //其他可操作
            $is_time = 1;
            $time_check =  $promotion->time>0 ? 1:0;
            $time_value = $promotion->time>0 ? $promotion->time:'';
            $time_disable = $promotion->time>0 ? 0:1;
            $time_placeholder = $promotion->time>0 ? '请输入优惠券的有效期（单位：天）':'该形式无需输入优惠券的有效期';;
        }
        $data = [
            'ticket'=>[
                'is_ticket'=>$is_ticket,
                'ticket_check'=>$ticket_check,
                'ticket_value'=>$ticket_value,
                'ticket_disable'=>$ticket_disable,
                'ticket_placeholder'=>$ticket_placeholder,
            ],
            'time'=>[
                'is_time'=>$is_time,
                'time_check'=>$time_check,
                'time_value'=>$time_value,
                'time_disable'=>$time_disable,
                'time_placeholder'=>$time_placeholder,
            ],
            'condition'=>[
                'is_condition'=>$is_condition,
                'condition_value'=>$condition_value,
                'condition_placeholder'=>$condition_placeholder,
            ],
            'discount'=>[
                'is_discount'=>$is_discount,
                'discount_value'=>$discount_value,
                'discount_placeholder'=>$discount_placeholder,
            ],
        ];
        return $this->showResult(200,'成功',$data);
    }
}
