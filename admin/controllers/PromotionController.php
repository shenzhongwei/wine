<?php

namespace admin\controllers;

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


    public function actionTargets(){
        $depDrop = Yii::$app->request->post('depdrop_parents');

        $results = [];
        if (isset($depDrop)) {
            $id = end($depDrop);

            switch($id){
                case 1: //平台
                    $query =array(['id'=>'1','name'=>'平台']);
                    break;
                case 2: //商家
                    $query=MerchantInfo::find()->select(['id','name'])->where(['is_active'=>1])->asArray()->all();
                    break;
                case 3: //店铺
                    $query=ShopInfo::find()->select(['id','name'])->where(['is_active'=>1])->asArray()->all();
                    break;
                case 4: //某商品
                    $query=GoodInfo::find()->select(['id','name'])->where(['is_active'=>1])->asArray()->all();
                    break;
                default:
                    $query =[];
                    break;
            }
            if(!empty($query)){
                $results = ArrayHelper::getColumn($query,function($element){
                    return [
                        'id'=>$element['id'],
                        'name'=>$element['name'],
                    ];
                });
            }
        }

        echo Json::encode(['output' => empty($results) ? '':$results, 'selected'=>'']);
        exit;
    }
}
