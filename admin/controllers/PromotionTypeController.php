<?php

namespace admin\controllers;

use kartik\form\ActiveForm;
use Yii;
use admin\models\PromotionType;
use admin\models\PromotionTypeSearch;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * PromotionTypeController implements the CRUD actions for PromotionType model.
 */
class PromotionTypeController extends BaseController
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
     * Lists all PromotionType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PromotionTypeSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
//        $dataProvider->pagination=[
//            'pageSize' => 15,
//        ];
        $dataProvider->sort = [
            'defaultOrder' => ['is_active'=>SORT_DESC]
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
        $model = new PromotionType();
        if(empty($id)){
            $model->id=$id;
        }
        $model->load($data);
        return ActiveForm::validate($model);
    }


    public function actionEnv(){
        $depDrop = Yii::$app->request->post('depdrop_parents');
        $results = [];
        if (isset($depDrop)) {
            $id = end($depDrop);
            if(!empty($id)){
                switch ($id){
                    case 1:
                        $results = [
                            [
                                'id'=>1,
                                'name'=>'用户注册',
                            ],
                            [
                                'id'=>2,
                                'name'=>'推荐成功',
                            ],
                        ];
                        break;
                    case 2:
                        $results = [
                            [
                                'id'=>3,
                                'name'=>'下单时',
                            ],
                        ];
                        break;
                    case 3:
                        $results = [
                            [
                                'id'=>4,
                                'name'=>'下单成功',
                            ],
                            [
                                'id'=>5,
                                'name'=>'被推荐人下单成功',
                            ],
                        ];
                        break;
                    case 4:
                        $results = [
                            [
                                'id'=>6,
                                'name'=>'充值成功',
                            ],
                        ];
                        break;
                    default:
                        $results = [];
                        break;

                }
            }
        }
        echo Json::encode(['output' => empty($results) ? '':$results, 'selected'=>'']);
        exit;
    }

    public function actionGroup(){
        $depDrop = Yii::$app->request->post('depdrop_parents');
        $results = [];
        if (isset($depDrop)) {
            $class = $depDrop[0];
            $env = $depDrop[1];
            if(!empty($class)&&!empty($env)){
                if($class==1){
                    if($env==1){
                        $results = [
                            [
                                'id'=>1,
                                'name'=>'赠送优惠券',
                            ],
                        ];
                    }elseif ($env==2){
                        $results = [
                            [
                                'id'=>1,
                                'name'=>'赠送优惠券',
                            ],
                            [
                                'id'=>2,
                                'name'=>'赠送积分',
                            ],
                        ];
                    }else{
                        $results = [];
                    }
                }elseif ($class==2){
                    $results = [
                        [
                        'id'=>4,
                        'name'=>'下单优惠',
                        ],
                    ];
                }elseif ($class==3){
                    if($env==4){
                        $results = [
//                            [
//                                'id'=>1,
//                                'name'=>'赠送优惠券',
//                            ],
//                            [
//                                'id'=>2,
//                                'name'=>'赠送积分',
//                            ],
                            [
                                'id'=>5,
                                'name'=>'分享网页',
                            ],
                        ];
                    }elseif ($env==5){
                        $results = [
                            [
                                'id'=>1,
                                'name'=>'赠送优惠券',
                            ],
                            [
                                'id'=>2,
                                'name'=>'赠送积分',
                            ],
                        ];
                    }else{
                        $results = [];
                    }
                }else{
                    $results = [
//                        [
//                            'id'=>1,
//                            'name'=>'赠送优惠券',
//                        ],
                        [
                            'id'=>2,
                            'name'=>'赠送积分',
                        ],
                        [
                            'id'=>3,
                            'name'=>'开通会员特权',
                        ],
                    ];
                }
            }
        }
        echo Json::encode(['output' => empty($results) ? '':$results, 'selected'=>'']);
        exit;
    }

    public function actionLimit(){
        $depDrop = Yii::$app->request->post('depdrop_parents');
        $results = [];
        if (isset($depDrop)) {
            $class = $depDrop[0];
            $env = $depDrop[1];
            $group = $depDrop[2];
            if(!empty($class)&&!empty($env)&&!empty($group)){
                if($class==1){
                    $results = [
                        [
                            'id'=>1,
                            'name'=>'唯一',
                        ],
                    ];
                }elseif ($class==2){
                    $results = [
                        [
                            'id'=>2,
                            'name'=>'多个共存',
                        ],
                    ];
                }else if($class==3){
                    if($env == 5){
                        $results = [
                            [
                                'id'=>2,
                                'name'=>'多个共存',
                            ],
                        ];
                    }else{
                        $results = [
                            [
                                'id'=>1,
                                'name'=>'唯一',
                            ],
                        ];
                    }
                }else{
                    if($group==2){
                        $results = [
                            [
                                'id'=>2,
                                'name'=>'多个共存',
                            ],
                        ];
                    }else{
                        $results = [
                            [
                                'id'=>1,
                                'name'=>'唯一',
                            ],
                        ];
                    }
                }
            }
        }
        echo Json::encode(['output' => empty($results) ? '':$results, 'selected'=>'']);
        exit;
    }

    /**
     * Updates an existing PromotionType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success','操作成功');
             return $this->redirect(['index']);
        } else {
            if(Yii::$app->request->isPost){
                Yii::$app->session->setFlash('danger',array_values($model->getFirstErrors())[0]);
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new PromotionType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PromotionType;

        if ($model->load(Yii::$app->request->post())) {
            $model->is_active=1;
            $model->active_at = time();
            $model->regist_at = time();
            if($model->save()){
                Yii::$app->session->setFlash('success','操作成功');
                return $this->redirect(['index']);
            }
        } else {
            if(Yii::$app->request->isPost){
                Yii::$app->session->setFlash('danger',array_values($model->getFirstErrors())[0]);
            }
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PromotionType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->is_active==1){
            $model->is_active=0;
        }else{
            $model->is_active=1;
        }
        $model->active_at=time();
        $model->save();
        Yii::$app->session->setFlash('success','操作成功');
        return $this->redirect(['index']);
    }

    /**
     * Finds the PromotionType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PromotionType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PromotionType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionPatch()
    {
        $keys = Yii::$app->request->post('keys');
        $button = Yii::$app->request->post('button');
        if(empty($keys)){
            return $this->showResult(304,'非法请求');
        }
        $ids = '('.implode(',',$keys).')';
        if($button == 'type_up'){
            $key = 'is_active';
            $value = 0;
            $valueTo = 1;
        }elseif($button == 'type_down'){
            $key = 'is_active';
            $value = 1;
            $valueTo = 0;
        }else{
            return $this->showResult(304,'非法请求');
        }
        $type = PromotionType::find()->where("$key=$value and id in $ids")->one();
        if(!empty($type)){
            $sql = "UPDATE promotion_type SET $key = $valueTo";
            if($key == 'is_active'){
                $sql .= " ,active_at=".time();
            }
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
