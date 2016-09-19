<?php

namespace admin\controllers;

use admin\models\GoodBrand;
use admin\models\GoodBreed;
use admin\models\GoodInfo;
use admin\models\MerchantInfo;
use admin\models\GoodSmell;
use Yii;
use admin\models\AdList;
use admin\models\AdListSearch;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
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
    public function actionIndex()
    {
        $searchModel = new AdListSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single AdList model.
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
     * Creates a new AdList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AdList;

        if (Yii::$app->request->post()) {
            $adposted=Yii::$app->request->post('AdList');

            //上传图片
            $picurl='';
            $img =UploadedFile::getInstance($model,'pic');
            $pic_path = '../../photo/ad/boot/';
            $img_temp='/ad/boot/';
            $temp_id=AdList::generateCode().'_'.date('md');
            if(!empty($img)){
                $ext = $img->getExtension();
                if(!is_dir($pic_path)){
                    @mkdir($pic_path,0777,true);
                }
                $logo_name =$temp_id.'.'.$ext;
                $res=$img->saveAs($pic_path.$logo_name);//设置图片的存储位置
                $picurl=$img_temp.$logo_name;
            }

            $transaction = Yii::$app->db->beginTransaction();
            try{
                if($adposted['type']==7){ //启动页
                    $query=AdList::find()->where(['type'=>$adposted['type'],'is_show'=>1])->one();
                    if(!empty($query)){
                        return $this->showResult(301,'启动页只能一张');
                    }
                }
                $model->attributes=$adposted;
                $model->pic=$picurl;

                $model->save();
            }catch(Exception $e){
                $transaction->rollBack();

            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'p1'=>'',
                'P'=>[]
            ]);
        }
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // 对广告图进行处理
        $p1 ='';$P= [];
        if ($model) {
            $p1 = Yii::$app->params['img_path'].$model->pic;
            $P = [
                'url' =>Url::toRoute('/ad/delete-img'),
                'key' => $model->id,
                'width'=>'200px'
            ];
        }

        if (Yii::$app->request->post()) {
            $adposted=Yii::$app->request->post('AdList');

            //上传图片
            $picurl='';
            $img =UploadedFile::getInstance($model,'pic');
            $pic_path = '../../photo/ad/boot/';
            $img_temp='/ad/boot/';
            $temp_id=AdList::generateCode().'_'.date('md');
            if(!empty($img)){
                $ext = $img->getExtension();
                if(!is_dir($pic_path)){
                    @mkdir($pic_path,0777,true);
                }
                $logo_name =$temp_id.'.'.$ext;
                $res=$img->saveAs($pic_path.$logo_name);//设置图片的存储位置
                $picurl=$img_temp.$logo_name;
            }

            $transaction = Yii::$app->db->beginTransaction();
            try{
                $model->attributes=$adposted;
                $model->pic=empty($picurl)?$adposted['pic_url']:$picurl;

                $model->save();
            }catch(Exception $e){
                $transaction->rollBack();

            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'p1'=>$p1,
                'P'=>$P
            ]);
        }
    }


    public function actionDelete($id)
    {
        $model=$this->findModel($id);
        if($model->is_show==1){
            $model->is_show=0;
        }else{
            $model->is_show=1;
        }
        $model->save();
        return $this->redirect(['index']);
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
                   $type =GoodBreed::find()->select(['id','name'])->asArray()->all();
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
                    $type=[[]];
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
        echo Json::encode(['output' => empty($results) ? '':$results, 'selected'=>'']);
        exit;
    }
}
