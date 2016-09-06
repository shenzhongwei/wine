<?php

namespace admin\controllers;

use admin\models\MerchantInfoSearch;
use admin\models\Zone;
use Yii;
use admin\models\MerchantInfo;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MerchantController implements the CRUD actions for MerchantInfo model.
 */
class MerchantController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' =>AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','update','create','delete','view','selectcity','selectdistrict'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions'=>[
                    'delete'=>['get','post'],
                ]
            ],
        ];
    }

    /**
     * Lists all MerchantInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MerchantInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        //获取所有的商户名称
        $mername=MerchantInfoSearch::getAllMerchant();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'mername'=>$mername
        ]);
    }

    /**
     * Displays a single MerchantInfo model.
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
     * Creates a new MerchantInfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $auth = Yii::$app->authManager;
        $item = $auth->getRolesByType(Yii::$app->user->identity->wa_type);
        $itemArr = ArrayHelper::map($item,'level','name');

        $model = new MerchantInfo;
        $model->is_active=1;

        if (Yii::$app->request->post()) {

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'item_arr'=>$itemArr
            ]);
        }
    }

    /**
     * Updates an existing MerchantInfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MerchantInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MerchantInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MerchantInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MerchantInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionSelectcity(){
        $p_id=Yii::$app->request->post('p_id');
        $data=Zone::getCity($p_id);
        return json_encode($data);
    }

    public function actionSelectdistrict(){
        $c_id=Yii::$app->request->post('c_id');
        $data=Zone::getDistrict($c_id);
        return json_encode($data);
    }
}
