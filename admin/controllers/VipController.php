<?php

namespace admin\controllers;

use kartik\widgets\ActiveForm;
use Yii;
use admin\models\GoodVip;
use admin\models\GoodVipSearch;
use admin\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * VipController implements the CRUD actions for GoodVip model.
 */
class VipController extends BaseController
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
     * Lists all GoodVip models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GoodVipSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $dataProvider->pagination = [
            'pageSize'=>15,
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
        if(empty($id)){
            $model = new GoodVip(['scenario'=>'add']);
        }else{
            $model = new GoodVip(['scenario'=>'update']);
        }
        $model->load($data);
        return ActiveForm::validate($model);
    }


    /**
     * Creates a new GoodVip model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GoodVip;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->runAction('index');
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GoodVip model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
//        $model->scenario = 'update';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->runAction('index');
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GoodVip model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = GoodVip::findOne($id);
        if($model->is_active==0){
            $model->is_active = 1;
        }else{
            $model->is_active = 0;
        }
        $model->save();
        return $this->runAction('index');
    }

    /**
     * Finds the GoodVip model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GoodVip the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GoodVip::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
