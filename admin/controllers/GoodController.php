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

    public function actionInfo(){
        $id = Yii::$app->request->get('id');
        $model = GoodInfo::findOne($id);
        echo '<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <meta name="HandheldFriendly" content="true">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0">
    <meta name="" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style>
        body{ font-size: 12px; line-height: 20px; color: #333; font-family: " Microsoft YaHei";}
        table{ width: 100% !important;}
        img{ width: 100%; background-size: 100% 100%;}
    </style>
</head>
<body>';
        echo empty($model)?'商品不存在':stripslashes($model->detail);
        echo '</body>';
        echo '</html>';
        exit;
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
            return $this->render('view', ['model' => $model]);
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
        $url = $model->pic;
        $post = Yii::$app->request->post();
        if(!empty($post)){
            $post['GoodInfo']['brand'] = empty($post['GoodInfo']['brand']) ? null:$post['GoodInfo']['brand'];
            $post['GoodInfo']['color'] = empty($post['GoodInfo']['color']) ? null:$post['GoodInfo']['color'];
            $post['GoodInfo']['smell'] = empty($post['GoodInfo']['smell']) ? null:$post['GoodInfo']['smell'];
            $post['GoodInfo']['dry'] = empty($post['GoodInfo']['dry']) ? null:$post['GoodInfo']['dry'];
            $post['GoodInfo']['boot'] = empty($post['GoodInfo']['boot']) ? null:$post['GoodInfo']['boot'];
            $post['GoodInfo']['country'] = empty($post['GoodInfo']['country']) ? null:$post['GoodInfo']['country'];
            $post['GoodInfo']['style'] = empty($post['GoodInfo']['style']) ? null:$post['GoodInfo']['style'];
            $post['GoodInfo']['breed'] = empty($post['GoodInfo']['breed']) ? null:$post['GoodInfo']['breed'];
        }
        if ($model->load($post)) {
            $pic = $model->pic;
            $extension = substr($pic,strrpos($pic,'.')+1);
            if($pic == '/goods/good_pic_'.$model->id.'.'.$extension){
                $filename = '/goods/good_pic_'.$model->id.'_'.time().'.'.$extension;
                @copy('../../photo'.$model->pic,'../../photo'.$filename);
                $model->pic = $filename;
            }
            if($model->save()){
                @unlink('../../photo'.$pic);
                if(!empty($url)){
                    @unlink('../../photo'.$url);
                }
                return $this->render('view', ['model' => $model]);
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
        return $this->redirect('index');
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
