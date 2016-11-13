<?php

namespace admin\controllers;

use admin\models\Admin;
use kartik\form\ActiveForm;
use Yii;
use admin\models\ShopInfo;
use admin\models\ShopSearch;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ShopController implements the CRUD actions for ShopInfo model.
 */
class ShopController extends BaseController
{

/**
     * Lists all ShopInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ShopSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $dataProvider->pagination=[
            'pageSize' => 15
        ];
        //获取所有的商户名称
//        $mername=MerchantInfoSearch::getAllMerchant();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionUpload(){
        $key = Yii::$app->request->post('key');
        $attr = Yii::$app->request->post('attr');
        $shopInfo = new ShopInfo();
        $file_name = "shop_".$key."_".time();
        if(Yii::$app->request->isPost) {
            $image = UploadedFile::getInstance($shopInfo, $attr);
            $path = '../../photo/shop/';
            if(!is_dir($path) || !is_writable($path)){
                FileHelper::createDirectory($path,0777,true);
            }
            $filePath = $path.'/'.$file_name.'.'.$image->extension;
            if( $image->saveAs($filePath)){
                echo json_encode([
                    'imageUrl'=>'/shop/'.$file_name.'.'.$image->extension,
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

    public function actionMap()
    {
        return $this->render('map');
    }

    /**
     * Displays a single ShopInfo model.
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

    public function actionValidForm(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $id=Yii::$app->request->get('id');
        $model = new ShopInfo();
        if(!empty($id)){
            $model->id = $id;
        }
        $model->load($data);
        return ActiveForm::validate($model);
    }

    public function actionCreate()
    {
        $model = new ShopInfo();
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->saveForm($model)) {
            Yii::$app->session->setFlash('success','操作成功');
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            if($post){
                Yii::$app->session->setFlash('danger','保存失败');
            }
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->save()) {
            Yii::$app->session->setFlash('success','操作成功');
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            if($post){
                Yii::$app->session->setFlash('danger','保存失败');
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->is_active==0){
            $model->is_active = 1;
        }else{
            $model->is_active = 0;
        }
        $model->active_at = time();
        if($model->save()){
            Yii::$app->session->setFlash('success','操作成功');
        }else{
            Yii::$app->session->setFlash('danger','操作失败');
        }
        return $this->redirect('index');
    }


    public function actionPatch()
    {
        $keys = Yii::$app->request->post('keys');
        $button = Yii::$app->request->post('button');
        if(empty($keys)){
            return $this->showResult(304,'非法请求');
        }
        $ids = '('.implode(',',$keys).')';
        if($button == 'shop_up'){
            $key = 'is_active';
            $value = 0;
            $valueTo = 1;
        }elseif($button == 'shop_down'){
            $key = 'is_active';
            $value = 1;
            $valueTo = 0;
        }elseif($button == 'shop_unlock'){
            $key = 'wa_lock';
            $value = 1;
            $valueTo = 0;
        }elseif($button == 'shop_lock'){
            $key = 'wa_lock';
            $value = 0;
            $valueTo = 1;
        }else{
            return $this->showResult(304,'非法请求');
        }
        if(in_array($button,['shop_up','shop_down'])){
            $table = 'shop_info';
            $models = ShopInfo::find()->where("$key=$value and id in $ids")->one();
        }else{
            $table = 'wine_admin';
            $models = Admin::find()->where("$key=$value and wa_id in (SELECT wa_id FROM shop_info WHERE id IN $ids)")->one();
        }
        if(!empty($models)){
            $sql = "UPDATE $table SET $key = $valueTo";
            if($key == 'is_active'){
                $sql .= " ,active_at=".time();
                $sql .= " WHERE id IN $ids AND $key=$value";
            }else{
                $sql.= " ,updated_time='".date('Y-m-d H:i:s')."'";
                $sql .= " WHERE wa_id in (SELECT wa_id FROM shop_info WHERE id IN $ids)";
            }
            $res = Yii::$app->db->createCommand($sql)->execute();
            if(!empty($res)){
                return $this->showResult(200,'操作成功');
            }else{
                return $this->showResult(400,'操作失败，请稍后重试');
            }
        }else{
            return $this->showResult(200,'操作成功');
        }
    }

    protected function findModel($id)
    {
        $model = ShopInfo::find()->joinWith('wa')->addSelect(['shop_info.*','wine_admin.wa_username as wa_username','concat("*****") as wa_password'])->where("id=$id")->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
