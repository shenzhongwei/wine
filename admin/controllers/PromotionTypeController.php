<?php

namespace admin\controllers;

use Yii;
use admin\models\PromotionType;
use admin\models\PromotionTypeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single PromotionType model.
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
     * Creates a new PromotionType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PromotionType;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
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
             return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
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
        $query =PromotionType::findOne([$id]);
        if(empty($query)){
            return $this->showResult(301,'未获取到该优惠券的信息');
        }
        //$query=$this->findModel($id);

        if($query->is_active==1){
            $query->is_active=0;
        }else{
            $query->is_active=1;
        }
        $query->active_at=time();
        $query->save();
//        if($query->save()) {
//            return $this->redirect(['view', 'id' => $query->id]);
//        }
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
}
