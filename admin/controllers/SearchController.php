<?php
namespace admin\controllers;

use admin\models\HotSearch;
use kartik\form\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use Yii;

/**
 * Created by PhpStorm.
 * User: 11633
 * Date: 2016/10/12
 * Time: 14:52
 */
class SearchController extends BaseController {



    public function behaviors()
    {
        return [
            'varbs'=>[
                'class'=>VerbFilter::className(),
                'actions'=>[
                    'delete'=>['post','get'],
                ]
            ]
        ];
    }

    public function actionIndex(){
        $query = HotSearch::find();
        $searchData = new ActiveDataProvider([
            'query'=>$query,
        ]);
        $searchData->sort->defaultOrder=['order'=>SORT_ASC];
        return $this->render('index',['dataProvider' => $searchData]);
    }

    public function actionValidForm(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $model = new HotSearch();
        $model->load($data);
        return ActiveForm::validate($model);
    }



    public function actionUpdate()
    {
        $hasEditable = Yii::$app->request->post('hasEditable');
        $id = Yii::$app->request->post('editableKey');
        if ($hasEditable && $id) {
            $model = $this->findModel($id);
            $arr = array_keys($_POST);
            $target = $arr[count($arr) - 1];
            if (!empty($model)) {
                $post[$target] = current($_POST[$target]);
                if(!empty($post[$target]['name'])){
                    $output = $post[$target]['name'];
                }
                if(!empty($post[$target]['order'])){
                    $output = $post[$target]['order'];
                }
                if ($model->load($post) && $model->save()) {
                    return json_encode(['output' => $output, 'message' => '']);
                } else {
                    return json_encode(['output' => '', 'message' => array_values($model->getFirstErrors())[0]]);
                }
            } else {
                return json_encode(['output' => '', 'message' => '未找到该条数据']);
            }
        } else {
            return json_encode(['output' => '', 'message' => '']);
        }
    }

    /**
     * Creates a new GoodVip model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HotSearch();
        $count = HotSearch::find()->count();
        if($count>=5){
            Yii::$app->session->setFlash('success','最多只能添加5条热搜');
            return $this->runAction('index');
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success','添加成功');
            return $this->runAction('index');
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id){
        $model = $this->findModel($id);
        if($model->delete()){
            Yii::$app->session->setFlash('success','删除成功');
        }else{
            Yii::$app->session->setFlash('danger','删除失败');
        }
        return $this->runAction('index');
    }

    /**
     * Finds the GoodType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HotSearch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HotSearch::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}