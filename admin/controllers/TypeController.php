<?php

namespace admin\controllers;

use admin\models\BootSearch;
use admin\models\BrandSearch;
use admin\models\BreedSearch;
use admin\models\ColorSearch;
use admin\models\CountrySearch;
use admin\models\DrySearch;
use admin\models\GoodBoot;
use admin\models\GoodBrand;
use admin\models\GoodBreed;
use admin\models\GoodColor;
use admin\models\GoodCountry;
use admin\models\GoodDry;
use admin\models\GoodModel;
use admin\models\GoodPic;
use admin\models\GoodPriceField;
use admin\models\GoodSmell;
use admin\models\GoodStyle;
use admin\models\ModelSearch;
use admin\models\PriceSearch;
use admin\models\SmellSearch;
use admin\models\StyleSearch;
use kartik\form\ActiveForm;
use Yii;
use admin\models\GoodType;
use admin\models\TypeSearch;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
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
        $smellSearch = new SmellSearch();
        $smellData = $smellSearch->search(Yii::$app->request->getQueryParams(),$id);
        $smellData->pagination = [
            'pageSize'=>15,
        ];
        $bootSearch = new BootSearch();
        $bootData = $bootSearch->search(Yii::$app->request->getQueryParams(), $id);
        $bootData->pagination = [
            'pageSize' => 15,
        ];
        $price = new GoodPriceField();
        $priceData = $price->search($id);
        $priceData->pagination=[
            'pageSize'=>15,
        ];
        $colorSearch = new ColorSearch();
        $colorData = $colorSearch->search(Yii::$app->request->getQueryParams(), $id);
        $colorData->pagination = [
            'pageSize' => 15,
        ];
        $breedSearch = new BreedSearch();
        $breedData = $breedSearch->search(Yii::$app->request->getQueryParams(), $id);
        $breedData->pagination = [
            'pageSize' => 15,
        ];
        $drySearch = new DrySearch();
        $dryData = $drySearch->search(Yii::$app->request->getQueryParams(), $id);
        $dryData->pagination = [
            'pageSize' => 15,
        ];
        $volumSearch = new ModelSearch();
        $volumData = $volumSearch->search(Yii::$app->request->getQueryParams(), $id);
        $volumData->pagination = [
            'pageSize' => 15,
        ];
        $countrySearch = new CountrySearch();
        $countryData = $countrySearch->search(Yii::$app->request->getQueryParams(), $id);
        $countryData->pagination = [
            'pageSize' => 15,
        ];
        $styleSearch = new StyleSearch();
        $styleData = $styleSearch->search(Yii::$app->request->getQueryParams(), $id);
        $styleData->pagination = [
            'pageSize' => 15,
        ];
        return $this->render('view',[
            'key'=>$key,
            'model'=>$model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'smellData' =>$smellData,
            'smellSearch'=>$smellSearch,
            'bootData' => $bootData,
            'bootSearch' => $bootSearch,
            'colorData' => $colorData,
            'colorSearch' => $colorSearch,
            'breedData' => $breedData,
            'priceData'=>$priceData,
            'breedSearch' => $breedSearch,
            'dryData' => $dryData,
            'drySearch' => $drySearch,
            'volumData' => $volumData,
            'volumSearch' => $volumSearch,
            'countryData' => $countryData,
            'countrySearch' => $countrySearch,
            'styleData' => $styleData,
            'styleSearch' => $styleSearch,
        ]);
    }

    public function actionValidForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $key = Yii::$app->request->get('key');
        if ($key == 'brand') {
            $model = new GoodBrand(['scenario' => 'create']);
        } elseif ($key == 'smell') {
            $model = new GoodSmell();
        } elseif ($key == 'boot') {
            $model = new GoodBoot();
        } elseif ($key == 'price') {
            $model = new GoodPriceField();
        } elseif ($key == 'color') {
            $model = new GoodColor();
        } elseif ($key == 'breed') {
            $model = new GoodBreed();
        } elseif ($key == 'dry') {
            $model = new GoodDry();
        } elseif ($key == 'volum') {
            $model = new GoodModel();
        } elseif ($key == 'country') {
            $model = new GoodCountry();
        } elseif ($key == 'style') {
            $model = new GoodStyle();
        } else {
            Yii::$app->session->setFlash('danger', '参数异常');
            return $this->runAction('index');
        }
        $model->load($data);
        return ActiveForm::validate($model);
    }

    /**
     * Creates a new GoodType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GoodType(['scenario'=>'create']);
        $model->regist_at = time();
        $model->is_active = 1;
        $model->active_at = time();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success','操作成功');
            return $this->runAction('index');
        } else {
            if(Yii::$app->request->post()){
                Yii::$app->session->setFlash('danger', '发生异常，请重试');
            }
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionChildCreate()
    {
        $key = Yii::$app->request->get('key');
        $type = Yii::$app->request->get('type');
        if ($key == 'brand') {
            $model = new GoodBrand(['scenario' => 'create']);
        } elseif ($key == 'smell') {
            $model = new GoodSmell();
        } elseif ($key == 'boot') {
            $model = new GoodBoot();
        } elseif ($key == 'color') {
            $model = new GoodColor();
        } elseif ($key == 'breed') {
            $model = new GoodBreed();
        } elseif ($key == 'dry') {
            $model = new GoodDry();
        } elseif ($key == 'volum') {
            $model = new GoodModel();
        } elseif ($key == 'country') {
            $model = new GoodCountry();
        } elseif ($key == 'style') {
            $model = new GoodStyle();
        } else {
            Yii::$app->session->setFlash('danger', '参数异常');
            return $this->runAction('index');
        }
        $model->regist_at = time();
        $model->is_active = 1;
        $model->active_at = time();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success','操作成功');
            return $this->redirect(['type/view', 'id' => $model->type, 'key' => $key]);
        } else {
            var_dump($model->getErrors());
            Yii::$app->session->setFlash('danger', '发生异常，请重试');
            return $this->redirect(['type/view', 'id' => $type, 'key' => $key]);
        }
    }

    public function actionPriceCreate(){
        $type = Yii::$app->request->get('type');
        $model = new GoodPriceField();
        $model->type = $type;
        if ($model->load(Yii::$app->request->post())) {
            $start = $model->start;
            $end = $model->end;
            $model->discription = "[$start,$end]";
            if($model->save()){
                Yii::$app->session->setFlash('success','操作成功');
                return $this->redirect(['type/view', 'id' => $model->type, 'key' => 'price']);
            }else{
                Yii::$app->session->setFlash('danger', '发生异常，请重试');
                return $this->redirect(['type/view', 'id' => $type, 'key' => 'price']);
            }
        } else {
            return $this->render('_pricecreate', [
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
                $post['GoodType'] = current($_POST['GoodType']);
                if(empty($post['GoodType']['name'])&&empty($url)){
                    if(isset($post['GoodType']['name'])){
                        return json_encode(['output'=>'', 'message'=>'请填写类型名称']);
                    }
                    if(empty($model->logo)){
                        return json_encode(['output'=>'', 'message'=>'请上传类型logo']);
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
//        $type = Yii::$app->request->get('type');
        $hasEditable = Yii::$app->request->post('hasEditable');
        $id = Yii::$app->request->post('editableKey');
        $url = empty($_POST['GoodBrand']['url']) ? '':$_POST['GoodBrand']['url'];
        if($hasEditable&&$id){
            $model = GoodBrand::findOne($id);
            if(!empty($model)){
                $post['GoodBrand'] = current($_POST['GoodBrand']);
                if(empty($post['GoodBrand']['name'])&&empty($url)&&empty($post['GoodBrand']['type'])){
                    if(isset($post['GoodBrand']['name'])){
                        return json_encode(['output'=>'', 'message'=>'请填写品牌名称']);
                    }
                    if(isset($post['GoodBrand']['type'])){
                        return json_encode(['output'=>'', 'message'=>'请选择类型']);
                    }
                    if(empty($model->logo)){
                        return json_encode(['output'=>'', 'message'=>'请上传品牌logo']);
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
                    return json_encode(['output'=>empty($post['GoodBrand']['type']) ? (empty($post['GoodBrand']['name']) ? (empty($post['GoodBrand']['logo']) ? '':Html::img('../../../photo'.$model->logo,[
                        'width'=>"70px",'height'=>"30px"])):$post['GoodBrand']['name']):$model->type0->name, 'message'=>'']);
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

    public function actionUpdateChild()
    {
        $hasEditable = Yii::$app->request->post('hasEditable');
        $id = Yii::$app->request->post('editableKey');
        $key = Yii::$app->request->get('key');
        if ($hasEditable && $id) {
            if ($key == 'smell') {
                $model = GoodSmell::findOne($id);
            } elseif ($key == 'boot') {
                $model = GoodBoot::findOne($id);
            } elseif ($key == 'color') {
                $model = GoodColor::findOne($id);
            } elseif ($key == 'breed') {
                $model = GoodBreed::findOne($id);
            } elseif ($key == 'dry') {
                $model = GoodDry::findOne($id);
            } elseif ($key == 'volum') {
                $model = GoodModel::findOne($id);
            } elseif ($key == 'country') {
                $model = GoodCountry::findOne($id);
            } elseif ($key == 'style') {
                $model = GoodStyle::findOne($id);
            } else {
                Yii::$app->session->setFlash('danger', '参数异常');
                return $this->runAction('index');
            }
            $arr = array_keys($_POST);
            $target = $arr[count($arr) - 1];
            if (!empty($model)) {
                $post[$target] = current($_POST[$target]);
                if (empty($post[$target]['name']) && empty($post[$target]['type'])) {
                    if (isset($post[$target]['name'])) {
                        return json_encode(['output' => '', 'message' => '请填写品牌名称']);
                    }
                    if (isset($post[$target]['type'])) {
                        return json_encode(['output' => '', 'message' => '请选择类型']);
                    }
                }
                if ($model->load($post) && $model->save()) {
                    return json_encode(['output' => empty($post[$target]['name']) ? $model->type0->name : $post[$target]['name'], 'message' => '']);
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


    public function actionPriceUpdate($id)
    {
        $type = Yii::$app->request->get('type');
        $model = GoodPriceField::find()->select(["SUBSTRING_INDEX(SUBSTRING_INDEX(discription,',',-1),']',1) as end",
            "SUBSTR(SUBSTRING_INDEX(discription,',',1),2) as start",'type','id'])->where(['id' => $id])->one();
        if(empty($model)){
            Yii::$app->session->setFlash('danger','未找到该条数据');
            return $this->redirect(['type/view', 'id' => $type, 'key' => 'price']);
        }else{
            if ($model->load(Yii::$app->request->post())) {
                $start = $model->start;
                $end = $model->end;
                $model->discription = "[$start,$end]";
                if($model->save()){
                    Yii::$app->session->setFlash('success','操作成功');
                    return $this->redirect(['type/view', 'id' => $model->type, 'key' => 'price']);
                }else{
                    Yii::$app->session->setFlash('danger', '发生异常，请重试');
                    return $this->redirect(['type/view', 'id' => $type, 'key' => 'price']);
                }
            } else {
                return $this->render('_priceupdate', [
                    'model' => $model,
                ]);
            }
        }
    }

    public function actionPriceUpdateForm($id){

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
        Yii::$app->session->setFlash('success', empty($model->is_active) ? '下架成功' : '上架成功');
        return $this->runAction('index');
    }


    public function actionChildDelete($id)
    {
        $key = Yii::$app->request->get('key');
        if ($key == 'brand') {
            $model = GoodBrand::findOne($id);
        } elseif ($key == 'smell') {
            $model = GoodSmell::findOne($id);
        } elseif ($key == 'boot') {
            $model = GoodBoot::findOne($id);
        } elseif ($key == 'color') {
            $model = GoodColor::findOne($id);
        } elseif ($key == 'breed') {
            $model = GoodBreed::findOne($id);
        } elseif ($key == 'dry') {
            $model = GoodDry::findOne($id);
        } elseif ($key == 'volum') {
            $model = GoodModel::findOne($id);
        } elseif ($key == 'country') {
            $model = GoodCountry::findOne($id);
        } elseif ($key == 'style') {
            $model = GoodStyle::findOne($id);
        } else {
            Yii::$app->session->setFlash('danger', '参数异常');
            return $this->runAction('index');
        }
        if(!empty($model)){
            $model->is_active = empty($model->is_active) ? 1:0;
            $model->active_at = time();
            $model->save();
            Yii::$app->session->setFlash('success', empty($model->is_active) ? '下架成功' : '上架成功');
        } else {
            Yii::$app->session->setFlash('danger', '未找到该数据');
        }
        return $this->redirect(['view', 'id' => $model->type, 'key' => $key]);
    }

    public function actionPriceDelete($id)
    {
        $model = GoodPriceField::findOne($id);
        if(!empty($model)){
            if($model->delete()){
                Yii::$app->session->setFlash('success','删除成功');
            }else{
                Yii::$app->session->setFlash('danger','删除失败');
            }
        }else{
            Yii::$app->session->setFlash('danger', '未找到该数据');

        }
        return $this->redirect(['view', 'id' => $model->type, 'key' => 'price']);
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
