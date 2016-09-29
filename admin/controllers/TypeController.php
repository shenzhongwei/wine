<?php

namespace admin\controllers;

use Yii;
use admin\models\GoodType;
use admin\models\TypeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TypeController implements the CRUD actions for GoodType model.
 */
class TypeController extends Controller
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
     * Lists all GoodType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TypeSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $dataProvider->pagination = [
            'pageSize'=>15,
        ];
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single GoodType model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->runAction(['index']);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    /**
     * Creates a new GoodType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GoodType;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->runAction(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GoodType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $hasEditable = Yii::$app->request->post('hasEditable');
        $id = Yii::$app->request->post('editableKey');
        if($hasEditable&&$id){
            $model = $this->findModel($id);
            if(!empty($model)){
                $post['GoodType'] = current($_POST['GoodType']);
                if($model->load($post)&&$model->save()){
                    return json_encode(['output'=>empty($post['GoodType']['name']) ? (empty($post['GoodType']['logo']) ? '':$post['GoodType']['logo']):$post['GoodType']['name'], 'message'=>'']);
                }else{
                    return json_encode(['output'=>'','message'=>array_values($model->getFirstErrors())[0]]);
                }
            }else{
                return json_encode(['output'=>'', 'message'=>'未找到该条数据']);
            }
        }else{
            return json_encode(['output'=>'', 'message'=>'']);
        }
    }

    /**
     * Deletes an existing GoodType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id);

        return $this->runAction(['index']);
    }

    /**
     * Finds the GoodType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GoodType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GoodType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
