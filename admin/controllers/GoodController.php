<?php

namespace admin\controllers;

use admin\models\MerchantInfo;
use admin\models\GoodType;
use common\helpers\ArrayHelper;
use Yii;
use admin\models\GoodInfo;
use admin\models\GoodSearch;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * GoodController implements the CRUD actions for GoodInfo model.
 */
class GoodController extends BaseController
{

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


    /**
     * Lists all GoodInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GoodSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $dataProvider->pagination = [
            'pageSize' => 15,
        ];
        $dataProvider->sort = [
            'defaultOrder' => ['is_active'=>SORT_DESC,'id'=>SORT_ASC]
        ];
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionUpload(){
        $goodInfo = new GoodInfo();
        $id = Yii::$app->request->post('id');
        if(empty($id)){
            $file_name = 'good_pic_'.time();
        }else{
            $file_name = 'good_pic_'.$id;
        }
        if(Yii::$app->request->isPost) {
            $image = UploadedFile::getInstance($goodInfo, 'img');
            $path = '../../photo/goods/';
            if(!is_dir($path) || !is_writable($path)){
                FileHelper::createDirectory($path,0777,true);
            }
            $filePath = $path.'/'.$file_name.'.'.$image->extension;
            if( $image->saveAs($filePath)){
                echo json_encode([
                    'imageUrl'=>'/goods/'.$file_name.'.'.$image->extension,
                    'error'=>'',
                ]);
                exit;
            }else{
                echo json_encode([
                    'imageUrl'=>'',
                    'error'=>'保存图片失败，请重试',
                ]);
                exit;
            }
        }else{
            echo json_encode([
                'imageUrl'=>'',
                'error'=>'未获取到图片信息',
            ]);
            exit;
        }
    }

    /**
     * Displays a single GoodInfo model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->render('view', ['model' => $model]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    public function actionDetail($id)
    {
        $model = $this->findModel($id);

        echo $model->detail;
        exit;
    }

    /**
     * Creates a new GoodInfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $admin = Yii::$app->user->identity;
        $model = new GoodInfo();
        $model->regist_at = time();
        $model->is_active = 1;
        $model->active_at = strtotime(date('Y-m-d 00:00:00'));
        $model->number = GoodInfo::generateCode().rand(1000,9999).date('is',time());
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            if($admin->wa_type>=2){
                $model->merchant = MerchantInfo::findOne(['wa_id'=>$admin->id])->id;
            }
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionChilds(){
        $key = Yii::$app->request->get('key');
        $depDrop = Yii::$app->request->post('depdrop_parents');
        $results = [];
        if (isset($depDrop)) {
            $id = end($depDrop);
            if(!empty($id)){
                $type = GoodType::findOne($id);
                if(!empty($type)){
                    $results = ArrayHelper::getColumn($type->$key,function($element){
                        return [
                            'id'=>$element->id,
                            'name'=>$element->name,
                        ];
                    });
                }
            }
        }
        echo Json::encode(['output' => empty($results) ? '':$results, 'selected'=>'']);
        exit;
    }

    /**
     * Updates an existing GoodInfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $pic = $model->pic;
            if($pic != 'good_pic_'.$model->id){
                $extension = substr($model->pic,strrpos($model->pic,'.')+1);
                @rename('../../photo'.$model->pic,'../../photo/goods/good_pic_'.$model->id.'.'.$extension);
                $model->pic = '/goods/good_pic_'.$model->id.'.'.$extension;
            }
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GoodInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->is_active==0){
            $model->is_active = 1;
        }else{
            $model->is_active = 0;
        }
        $model->active_at = time();
        $model->save();
        return $this->runAction('index');
    }



    /**
     * Finds the GoodInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GoodInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GoodInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
