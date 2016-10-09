<?php

namespace admin\controllers;

use admin\models\BrandSearch;
use admin\models\GoodBrand;
use Yii;
use admin\models\GoodType;
use admin\models\TypeSearch;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * TypeController implements the CRUD actions for GoodType model.
 */
class TypeController extends BaseController
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


    public function actionUpload(){
        $goodType = new GoodType();
        $file_name = 'good_type_'.time();

        if(Yii::$app->request->isPost) {
            $image = UploadedFile::getInstance($goodType, 'url');
            $path = '../../photo/type/';
            if(!is_dir($path) || !is_writable($path)){
                FileHelper::createDirectory($path,0777,true);
            }
            $filePath = $path.'/'.$file_name.'.'.$image->extension;
            if( $image->saveAs($filePath)){
                echo json_encode([
                    'imageUrl'=>'/type/'.$file_name.'.'.$image->extension,
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

    public function actionBrandUpload(){
        $goodBrand = new GoodBrand();
        $file_name = 'good_brand_'.time();

        if(Yii::$app->request->isPost) {
            $image = UploadedFile::getInstance($goodBrand, 'url');
            $path = '../../photo/brand/';
            if(!is_dir($path) || !is_writable($path)){
                FileHelper::createDirectory($path,0777,true);
            }
            $filePath = $path.'/'.$file_name.'.'.$image->extension;
            if( $image->saveAs($filePath)){
                echo json_encode([
                    'imageUrl'=>'/brand/'.$file_name.'.'.$image->extension,
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

    public function actionView($id){
        $model = $this->findModel($id);
        $key = Yii::$app->request->get('key');
        $searchModel = new BrandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(),$id);
        $dataProvider->pagination=[
            'pageSize'=>15,
        ];
        return $this->render('view',[
            'key'=>$key,
            'model'=>$model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel]);
    }


    /**
     * Creates a new GoodType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GoodType(['scenario'=>'create']);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->runAction('index');
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionCreateBrand()
    {
        $type = Yii::$app->request->get('type');
        $model = new GoodBrand(['scenario'=>'create']);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->runAction('index',['id'=>$type,'key'=>'brand']);
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
        $url = empty($_POST['GoodType']['url']) ? '':$_POST['GoodType']['url'];
        if($hasEditable&&$id){
            $model = $this->findModel($id);
            if(!empty($model)){
                $url = empty($url) ? $model->logo:$url;
                $post['GoodType'] = current($_POST['GoodType']);
                if(empty($post['GoodType']['name'])&&empty($url)){
                    if(empty($post['GoodType']['name'])&&empty($model->name)){
                        return json_encode(['output'=>'', 'message'=>'请填写类型名称']);
                    }
                    if(empty($url)){
                        return json_encode(['output'=>'', 'message'=>'上传类型图标']);
                    }
                }
                if(!empty($url)){
                    $post['GoodType']=[
                        'logo'=>$url,
                    ];
                }
                $pic = $model->logo;
                $model->scenario='update';
                if($model->load($post)&&$model->save()){
                    if(!empty($post['GoodType']['logo'])&&$post['GoodType']['logo']!=$pic && !empty($pic)&&!empty($url)){
                        @unlink('../../photo'.$pic);
                    }
                    return json_encode(['output'=>empty($post['GoodType']['name']) ? (empty($post['GoodType']['logo']) ? '':Html::img('../../../photo'.$model->logo,[
                        'width'=>"20px",'height'=>"20px"])):$post['GoodType']['name'], 'message'=>'']);
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


    public function actionUpdateBrand()
    {
        $hasEditable = Yii::$app->request->post('hasEditable');
        $id = Yii::$app->request->post('editableKey');
        $url = empty($_POST['GoodBrand']['url']) ? '':$_POST['GoodBrand']['url'];
        if($hasEditable&&$id){
            $model = GoodBrand::findOne($id);
            if(!empty($model)){
                $url = empty($url) ? $model->logo:$url;
                $post['GoodBrand'] = current($_POST['GoodBrand']);
                if(empty($post['GoodBrand']['name'])&&empty($url)){
                    if(empty($post['GoodBrand']['name'])&&empty($model->name)){
                        return json_encode(['output'=>'', 'message'=>'请填写类型名称']);
                    }
                    if(empty($url)){
                        return json_encode(['output'=>'', 'message'=>'上传类型图标']);
                    }
                }
                if(!empty($url)){
                    $post['GoodBrand']=[
                        'logo'=>$url,
                    ];
                }
                $pic = $model->logo;
                $model->scenario='update';
                if($model->load($post)&&$model->save()){
                    if(!empty($post['GoodBrand']['logo'])&&$post['GoodBrand']['logo']!=$pic && !empty($pic)&&!empty($url)){
                        @unlink('../../photo'.$pic);
                    }
                    return json_encode(['output'=>empty($post['GoodBrand']['name']) ? (empty($post['GoodBrand']['logo']) ? '':Html::img('../../../photo'.$model->logo,[
                        'width'=>"20px",'height'=>"20px"])):$post['GoodBrand']['name'], 'message'=>'']);
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
        $model = $this->findModel($id);
        if(!empty($model)){
            $model->is_active = empty($model->is_active) ? 1:0;
            $model->active_at = time();
            $model->save();
        }
        return $this->runAction('index');
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
