<?php

namespace admin\controllers;

use admin\models\GoodBrand;
use admin\models\GoodBreed;
use admin\models\GoodInfo;
use admin\models\GoodType;
use admin\models\MerchantInfo;
use admin\models\GoodSmell;
use kartik\form\ActiveForm;
use Yii;
use admin\models\AdList;
use admin\models\AdListSearch;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * AdController implements the CRUD actions for AdList model.
 */
class AdController extends BaseController
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
     * Lists all AdList models.
     * @return mixed
     */
    public function actionBoot()
    {
        $searchModel = new AdListSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(),7,0);

        $dataProvider->sort = [
            'defaultOrder' => ['is_show'=>SORT_DESC]
        ];

        return $this->render('boot', [
            'dataProvider' => $dataProvider,
//            'searchModel' => $searchModel,
        ]);
    }

    public function actionHead()
    {
        $searchModel = new AdListSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(),0,1);
        $dataProvider->sort = [
            'defaultOrder' => ['is_show'=>SORT_DESC]
        ];

        return $this->render('head', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMiddle()
    {
        $searchModel = new AdListSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(),0,2);
        $dataProvider->sort = [
            'defaultOrder' => ['is_show'=>SORT_DESC]
        ];

        return $this->render('middle', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionValidForm(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $key = Yii::$app->request->get('key');
        $data = Yii::$app->request->post();
        $model = new AdList();
        if(!empty($data['AdList']['id'])){
            $model->id = $data['AdList']['id'];
        }
        if($key=='boot'){
            $model->type=7;
            $model->postion=0;
            $model->target_id=0;
        }elseif ($key=='head'){
            $model->postion = 1;
        }elseif ($key == 'middle'){
            $model->postion = 2;
        }
        if($data['AdList']['type']==1){
            $model->target_id=0;
        }elseif ($data['AdList']['type']==8){
            $model->target_id=0;
            $model->pic_url='';
        }
        $model->load($data);
        return ActiveForm::validate($model);
    }

    public function actionUpload(){
        $goodType = new AdList();
        $file_name = 'wine_ad_'.time();
        $key = Yii::$app->request->post('key');
        if(Yii::$app->request->isPost) {
            $image = UploadedFile::getInstance($goodType, 'url');
            $path = '../../photo/ad/'.$key;
            if(!is_dir($path) || !is_writable($path)){
                FileHelper::createDirectory($path,0777,true);
            }
            $filePath = $path.'/'.$file_name.'.'.$image->extension;
            if( $image->saveAs($filePath)){
                echo json_encode([
                    'imageUrl'=>'/ad/'.$key.'/'.$file_name.'.'.$image->extension,
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
     * Displays a single AdList model.
     * @param integer $id
     * @return mixed
     */

    /**
     * Creates a new AdList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $key = Yii::$app->request->get('key');
        $model = new AdList;
        if($key=='boot'){
            $model->type=7;
            $model->target_id=0;
            $model->postion = 0;
        }elseif ($key=='head'){
            $model->postion=1;
        }elseif ($key == 'middle'){
            $model->postion = 2;
        }
        $post = Yii::$app->request->post();
        if($post){
            if(in_array($post['AdList']['type'],[1,7])){
                $model->target_id=0;
            }elseif ($post['AdList']['type']==8){
                $model->target_id=0;
                $model->pic_url='';
            }elseif (in_array($post['AdList']['type'],[2,3,4,5,6])){
                $model->pic_url='';
            }
        }
        if ($model->load($post) && $model->save()) {
            Yii::$app->session->setFlash('success','保存成功');
            return $this->redirect([$key]);
        } else {
            if(Yii::$app->request->isPost){
                Yii::$app->session->setFlash('danger',array_values($model->getFirstErrors())[0]);
                return $this->redirect([$key]);
            }
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    public function actionUpdate($id)
    {
        $key = Yii::$app->request->get('key');
        $model = $this->findModel($id);
        if($key=='boot'){
            $model->type=7;
            $model->target_id=0;
            $model->postion = 0;
        }elseif ($key=='head'){
            $model->postion=1;
        }elseif ($key == 'middle'){
            $model->postion = 2;
        }
        $post = Yii::$app->request->post();
        if($post){
            if(in_array($post['AdList']['type'],[1,7])){
                $model->target_id=0;
            }elseif ($post['AdList']['type']==8){
                $model->target_id=0;
                $model->pic_url='';
            }elseif (in_array($post['AdList']['type'],[2,3,4,5,6])){
                $model->pic_url='';
            }
        }
        if ($model->load($post) && $model->save()) {
            Yii::$app->session->setFlash('success','保存成功');
            return $this->redirect([$key]);
        } else {
            if(Yii::$app->request->isPost){
                Yii::$app->session->setFlash('danger',array_values($model->getFirstErrors())[0]);
                return $this->redirect([$key]);
            }else{
                    if(in_array($model->type,[1,7])){
                        $model->target_id=0;
                    }elseif ($model->type==8){
                        $model->target_id=0;
                        $model->pic_url='';
                    }elseif (in_array($model->type,[2,3,4,5,6])){
                        $model->pic_url='';
                    }
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    public function actionDelete($id)
    {
        $name = Yii::$app->request->get('name');
        $model=$this->findModel($id);
        $model->is_show = 0;
        $model->save();
        Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect([$name]);
    }

    /**
     * Finds the AdList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * 根据相应类型查找对应的名称
     */
    public function actionRelationName(){
        $depDrop = Yii::$app->request->post('depdrop_parents');
        $results = [];
        if (isset($depDrop)) {
            $id = end($depDrop); //输出数组中最后一个元素
            switch($id){
                case 1:
                    $type=array(
                        array(
                            "id"=>"0",
                            "name"=>"外部网页",
                        )
                    );
                   break;
                case 2:  //商品广告
                   $type =GoodInfo::find()->select(['id','name'])->asArray()->all();
                   break;
                case 3:  //品牌广告
                   $type=GoodBrand::find()->select(['id','name'])->asArray()->all();
                   break;
                case 4: //商家广告
                   $type=MerchantInfo::find()->select(['id','name'])->asArray()->all();
                   break;
                case 5: //香型广告
                   $type =GoodSmell::find()->select(['id','name'])->asArray()->all();
                   break;
                case 6: //类型广告
                   $type =GoodType::find()->select(['id','name'])->asArray()->all();
                   break;
                case 7:
                    $type=array(
                        array(
                            "id"=>"0",
                            "name"=>"启动页",
                        )
                    );
                   break;
                case 8:
                    $type=array(
                        array(
                            "id"=>"0",
                            "name"=>"充值页",
                        )
                    );
                    break;
                default:
                    $type=[];
                    break;
            }
            if(!empty($type)){
                $results = ArrayHelper::getColumn($type,function($element){
                   return [
                      'id'=>$element['id'],
                      'name'=>$element['name'],
                   ];
                });
            }
        }
        echo Json::encode(['output' =>  empty($results) ? '':$results, 'selected'=>'']);
        exit;
    }
}
