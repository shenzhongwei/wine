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
//                var_dump($model->getErrors());
//                exit;
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
                if ($type->env == 5) {
                    $model = Dics::find()->where("type='优惠适用对象'")->all();
                } else {
                    $model = Dics::find()->where("type='优惠适用对象' and id=1")->all();
                }
                $res = ArrayHelper::getColumn($model,function($element){
                    return [
                        'id'=>$element->id,
                        'name'=>$element->name
                    ];
                });
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
        $style = Yii::$app->request->post('style');
        if(empty($type)||empty($style)){
            $conditionDisable = 1;
            $conditionVlaue = '';
            $conditionPlaceholder = '请先选择优惠形式';
            $discountDisable = 1;
            $discountValue = '';
            $discountPlaceholder = '请先选择优惠形式';
        }else{
            $promotionType = PromotionType::findOne($type);
            if(empty($promotionType)){
                return $this->showResult(301,'未获取到优惠种类信息');
            }
            if($style==1){
                if($promotionType->group==3){
                    $conditionDisable = 0;
                    $conditionVlaue = '';
                    $conditionPlaceholder = '请输入开通会员条件';
                    $discountDisable = 1;
                    $discountValue = '';
                    $discountPlaceholder = '该优惠种类无需输入优惠额';
                }elseif (($promotionType->env==2&&$promotionType->group==2)||($promotionType->env==5&&$promotionType->group==2)){
                    $conditionDisable = 1;
                    $conditionVlaue = '';
                    $conditionPlaceholder = '该优惠种类无需输入条件';
                    $discountDisable = 0;
                    $discountValue = '';
                    $discountPlaceholder = '请输入优惠额度';
                }else{
                    $conditionDisable = 0;
                    $conditionVlaue = '';
                    $conditionPlaceholder = '请输入优惠条件';
                    $discountDisable = 0;
                    $discountValue = '';
                    $discountPlaceholder = '请输入优惠额';
                }
            }else{
                $conditionDisable = 1;
                $conditionVlaue = '';
                $conditionPlaceholder = '该优惠种类无需输入条件';
                $discountDisable = 0;
                $discountValue = '';
                $discountPlaceholder = '请输入优惠百分比';
            }
        }
        $data = [
            'conditionDisable'=>$conditionDisable,
            'conditionValue'=>$conditionVlaue,
            'conditionPlaceholder'=>$conditionPlaceholder,
            'discountDisable'=>$discountDisable,
            'discountValue'=>$discountValue,
            'discountPlaceholder'=>$discountPlaceholder,
        ];
        return $this->showResult(200,'成功',$data);
    }

    public function actionTypeChange(){
        $user_id = Yii::$app->user->identity;
        if(empty($user_id)){
            return $this->showResult(302,'读取用户信息出错');
        }
        $type = Yii::$app->request->post('type');
        if(!empty($type)){
            $promotionType = PromotionType::findOne($type);
            if(!empty($promotionType)){
                if($promotionType->env==1||$promotionType->group==3){
                    $timeDisable = 1;
                    $timeValidDisable = 1;
                    $timeValidValue = 1;
                    $timeValue = '1';
                    $timePlaceHolder = '';
                }elseif(in_array($promotionType->env,[2,5,6])){
                    $timeDisable = 1;
                    $timeValidDisable = 0;
                    $timeValidValue = '';
                    $timeValue = '';
                    $timePlaceHolder = '请先选择参与次数的形式';
                }else{
                    $timeDisable = 1;
                    $timeValidDisable = 1;
                    $timeValidValue = 0;
                    $timeValue = '';
                    $timePlaceHolder = '该优惠种类无需输入参与次数';
                }
                if($promotionType->group==1||$promotionType->group==5){
                    $ticketDisable = 1;
                    $ticketValidDisable = 0;
                    $ticketValidValue = '';
                    $ticketValue = '';
                    $ticketPlaceHolder = '请先选择优惠券的有效期的形式';
                }else{
                    $ticketDisable = 1;
                    $ticketValidDisable = 1;
                    $ticketValidValue = '';
                    $ticketValue = '';
                    $ticketPlaceHolder = '该优惠种类无需选择优惠券的有效期形式';
                }
                $data = [
                    'timeDisable'=>$timeDisable,
                    'timeValidDisable'=>$timeValidDisable,
                    'timeValidValue'=>$timeValidValue,
                    'timeVlaue'=>$timeValue,
                    'timePlaceHolder'=>$timePlaceHolder,
                    'ticketDisable'=>$ticketDisable,
                    'ticketValidDisable'=>$ticketValidDisable,
                    'ticketValidValue'=>$ticketValidValue,
                    'ticketVlaue'=>$ticketValue,
                    'ticketPlaceHolder'=>$ticketPlaceHolder,
                ];
                return $this->showResult(200,'成功',$data);
            }else{
                return $this->showResult(301,'未获取到优惠种类信息');
            }
        }else{
            return $this->showResult(301,'未获取到优惠种类信息');
        }
    }

    public function actionPromotion()
    {
        $user_id = Yii::$app->user->identity;
        if (empty($user_id)) {
            return $this->showResult(302, '读取用户信息出错');
        }
        $id = Yii::$app->request->post('id');
        $promotion = PromotionInfo::findOne($id);
        if (empty($promotion) || empty($promotion->pt)) {
            $conditionDisable = 1;
            $conditionVlaue = '';
            $conditionPlaceholder = '请先选择优惠形式';
            $discountDisable = 1;
            $discountValue = '';
            $discountPlaceholder = '请先选择优惠形式';
            $timeDisable = 1;
            $timeValidDisable = 1;
            $timeValidValue = 1;
            $timeValue = '';
            $timePlaceHolder = '请先选择参与次数形式';
            $ticketDisable = 1;
            $ticketValidDisable = 0;
            $ticketValidValue = '';
            $ticketValue = '';
            $ticketPlaceHolder = '请先选择优惠券的有效期的形式';
        } else {
            $promotionType = $promotion->pt;
            $style = $promotion->style;
            if ($promotionType->env == 1 || $promotionType->group == 3) {//注册或者开会员
                $timeDisable = 1;
                $timeValidDisable = 1;
                $timeValidValue = 1;
                $timeValue = $promotion->time;
                $timePlaceHolder = '';
            } elseif (in_array($promotionType->env, [2, 5, 6])) {//充值送积分，推荐活动，推荐下单活动，限制次数
                $timeDisable = $promotion->time>0 ? 0:1;
                $timeValidDisable = 0;
                $timeValidValue = $promotion->time>0 ? 1:0;
                $timeValue = $promotion->time>0 ? $promotion->time:'';
                $timePlaceHolder = $promotion->time>0 ? '请先选择参与次数的形式':'该形式无需输入使用次数';
            } else {//其他无需次数
                $timeDisable = 1;
                $timeValidDisable = 1;
                $timeValidValue = 0;
                $timeValue = '';
                $timePlaceHolder = '该优惠种类无需输入参与次数';
            }
            if ($promotionType->group == 1 || $promotionType->group == 5) {//发券形式 需要券有效期
                $ticketDisable = $promotion->valid_circle>0 ? 0:1;
                $ticketValidDisable = 0;
                $ticketValidValue = $promotion->valid_circle>0 ? 1:0;
                $ticketValue = $promotion->valid_circle>0 ? $promotion->valid_circle:'';
                $ticketPlaceHolder = $promotion->valid_circle>0 ? '请先选择优惠券的有效期的形式':'该形式无需输入优惠券的有消息';
            } else {//其他无需
                $ticketDisable = 1;
                $ticketValidDisable = 1;
                $ticketValidValue = '';
                $ticketValue = '';
                $ticketPlaceHolder = '该优惠种类无需选择优惠券的有效期形式';
            }
            if($style==1){//固定的
                if($promotionType->group==3){//开会员条件需要的
                    $conditionDisable = 0;
                    $conditionVlaue = $promotion->condition;
                    $conditionPlaceholder = '请输入开通会员条件';
                    $discountDisable = 1;
                    $discountValue = '';
                    $discountPlaceholder = '该优惠种类无需输入优惠额';
                }elseif (($promotionType->env==2&&$promotionType->group==2)||($promotionType->env==5&&$promotionType->group==2)){//积分推荐和下单
                    $conditionDisable = 1;
                    $conditionVlaue = '';
                    $conditionPlaceholder = '该优惠种类无需输入条件';
                    $discountDisable = 0;
                    $discountValue = $promotion->discount;
                    $discountPlaceholder = '请输入优惠额度';
                }else{//其他都要输入
                    $conditionDisable = 0;
                    $conditionVlaue = $promotion->condition;
                    $conditionPlaceholder = '请输入优惠条件';
                    $discountDisable = 0;
                    $discountValue = $promotion->discount;
                    $discountPlaceholder = '请输入优惠额';
                }
            }else{//百分比
                $conditionDisable = 1;
                $conditionVlaue = '';
                $conditionPlaceholder = '该优惠种类无需输入条件';
                $discountDisable = 0;
                $discountValue = $promotion->discount;
                $discountPlaceholder = '请输入优惠百分比';
            }
        }
        $data = [
            'timeDisable'=>$timeDisable,
            'timeValidDisable'=>$timeValidDisable,
            'timeValidValue'=>$timeValidValue,
            'timeVlaue'=>$timeValue,
            'timePlaceHolder'=>$timePlaceHolder,
            'ticketDisable'=>$ticketDisable,
            'ticketValidDisable'=>$ticketValidDisable,
            'ticketValidValue'=>$ticketValidValue,
            'ticketVlaue'=>$ticketValue,
            'ticketPlaceHolder'=>$ticketPlaceHolder,
            'conditionDisable'=>$conditionDisable,
            'conditionValue'=>$conditionVlaue,
            'conditionPlaceholder'=>$conditionPlaceholder,
            'discountDisable'=>$discountDisable,
            'discountValue'=>$discountValue,
            'discountPlaceholder'=>$discountPlaceholder,
        ];
        return $this->showResult(200, '成功', $data);
    }

    public function actionPatch(){
        $button = Yii::$app->request->post('button');
        $keys = Yii::$app->request->post('keys');
        if(empty($keys)){
            return $this->showResult(304,'非法请求');
        }
        $ids = '('.implode(',',$keys).')';
        if($button == 'promotion_up'){
            $key = 'is_active';
            $value = 0;
            $valueTo = 1;
        }elseif($button == 'promotion_down') {
            $key = 'is_active';
            $value = 1;
            $valueTo = 0;
        }else{
            return $this->showResult(304,'非法请求');
        }
        $rushes = PromotionInfo::find()->where("$key=$value and id in $ids")->one();
        if(!empty($rushes)){
            $sql = "UPDATE promotion_info SET $key = $valueTo,active_at=".time();
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
}
