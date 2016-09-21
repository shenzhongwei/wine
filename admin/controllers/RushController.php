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
class RushController extends Controller
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->runAction(['index']);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->runAction(['index']);
        } else {
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
        return $this->runAction('index');
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
