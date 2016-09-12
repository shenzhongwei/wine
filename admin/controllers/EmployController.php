<?php

namespace admin\controllers;

use admin\models\MerchantInfoSearch;
use admin\models\ShopSearch;
use kartik\widgets\ActiveForm;
use Yii;
use admin\models\EmployeeInfo;
use admin\models\EmployeeInfoSearch;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EmployController implements the CRUD actions for EmployeeInfo model.
 */
class EmployController extends Controller
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
        $searchModel = new EmployeeInfoSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());


        /*********************在gridview列表页面上直接修改数据 start*****************************************/
        //获取前面一部传过来的值
        if (Yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey'); //获取需要编辑的id
            $model = $this->findModel($id);
            $out = Json::encode(['output'=>'', 'message'=>'']);
            //获取用户修改的参数（比如：手机号）
            $posted = current($_POST['EmployeeInfo']); //输出数组中当前元素的值，默认初始指向插入到数组中的第一个元素。移动数组内部指针，使用next()和prev()

            $post = ['EmployeeInfo' => $posted];
            $output = '';
            if ($model->load($post)) { //赋值
                $model->status=$posted['status'];
                $model->save(); //save()方法会先调用validate()再执行insert()或者update()
                isset($posted['status']) && $output=EmployeeInfoSearch::getEmploySattus($model); //配送人员当前状态
            }
            $out = Json::encode(['output'=>$output, 'message'=>'']);
            echo $out;
            return;
        }
        /*******************在gridview列表页面上直接修改数据 end***********************************************/
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
        $model = new EmployeeInfo;

        if ($model->load(Yii::$app->request->post())){

            $employ=Yii::$app->request->post('EmployeeInfo');
            $model->attributes=$employ;
            $model->register_at=time();
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EmployeeInfo model.
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
     * Deletes an existing EmployeeInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {

        $model=$this->findModel($id);
        $model->status=0;
        $model->save();
        return $this->redirect(['index']);
    }

    /**
     * Finds the EmployeeInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmployeeInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmployeeInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionOwners(){
        $depDrop = Yii::$app->request->post('depdrop_parents');
        $results = [];
        if (isset($depDrop)) {
            $id = end($depDrop);
            switch($id){
                case 0: //商家
                        $model=MerchantInfoSearch::find()->where(['is_active'=>1])->all();
                    break;
                case 1: //门店
                        $model=ShopSearch::find()->where(['is_active'=>1])->all();
                    break;
                default: $model=''; break;
            }
            if(!empty($model)){
                $results = ArrayHelper::getColumn($model,function($element){
                    return [
                        'id'=>$element->id,
                        'name'=>$element->name,
                    ];
                });
            }
        }
        echo Json::encode(['output' => empty($results) ? '':$results, 'selected'=>'']);
        exit;
    }
}
