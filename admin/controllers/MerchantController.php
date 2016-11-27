<?php

namespace admin\controllers;

use admin\models\Admin;
use admin\models\MerchantInfoSearch;
use admin\models\OrderInfo;
use admin\models\UploadForm;
use admin\models\Zone;
use kartik\form\ActiveForm;
use Yii;
use admin\models\MerchantInfo;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * MerchantController implements the CRUD actions for MerchantInfo model.
 */
class MerchantController extends BaseController
{

    /**
     * Lists all MerchantInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MerchantInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $dataProvider->pagination=[
            'pageSize' => 15
        ];
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionMap()
    {
        return $this->render('map');
    }

    public function actionUpload(){
        $attr = Yii::$app->request->post('attr');
        $shopInfo = new MerchantInfo();
        $file_name = "merchant"."_".time();
        if(Yii::$app->request->isPost) {
            $image = UploadedFile::getInstance($shopInfo, $attr);
            if(!empty($image)){
                $path = '../../photo/merchant/';
                if(!is_dir($path) || !is_writable($path)){
                    FileHelper::createDirectory($path,0777,true);
                }
                $filePath = $path.'/'.$file_name.'.'.$image->extension;
                if( $image->saveAs($filePath)){
                    echo json_encode([
                        'imageUrl'=>'/merchant/'.$file_name.'.'.$image->extension,
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
                    'error'=>'请重新选择图片后上传',
                ]);
                exit;
            }
        }else{
            echo json_encode([
                'imageUrl'=>'',
                'error'=>'请重新选择图片后上传',
            ]);
            exit;
        }
    }

    /**
     * Displays a single ShopInfo model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if(!empty($model)){
            $shops = $model->shopInfos;
            if(empty($shops)){
                $shopId = '(0)';
            }else{
                $shopId = '('.implode(',',ArrayHelper::getColumn($shops,'id')).')';
            }
            $query = OrderInfo::find()->where("sid in $shopId");
            $query->addSelect(['order_info.*','(CASE state WHEN 1 THEN 98 ELSE state END) as step']);
            $merchantOrders = new ActiveDataProvider([
                'query'=>$query,
            ]);
            $sort = $merchantOrders->getSort();
            $sort->attributes['step'] = [
                'asc' => ['step' => SORT_ASC],
                'desc' => ['step' => SORT_DESC],
                'label' => 'step',
            ];
            $sort->defaultOrder = ['step'=>SORT_ASC,'order_date'=>SORT_ASC];
            $merchantOrders->pagination = [
                'pageSize'=>5,
            ];
            return $this->render('view', ['model' => $model,'orders'=>$merchantOrders]);
        }else{
            Yii::$app->session->setFlash('danger','商户信息异常');
            return $this->redirect('index');
        }
    }

    public function actionValidForm(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $id=Yii::$app->request->get('id');
        $model = new MerchantInfo();
        if(!empty($id)){
            $model->id = $id;
        }
        $model->load($data);
        return ActiveForm::validate($model);
    }


    public function actionCreate()
    {
        $model = new MerchantInfo();
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
        if ($model->load($post) && $model->updateForm($model)) {
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

    /*
     * 删除
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
        if($button == 'merchant_up'){
            $key = 'is_active';
            $value = 0;
            $valueTo = 1;
        }elseif($button == 'merchant_down'){
            $key = 'is_active';
            $value = 1;
            $valueTo = 0;
        }elseif($button == 'merchant_unlock'){
            $key = 'wa_lock';
            $value = 1;
            $valueTo = 0;
        }elseif($button == 'merchant_lock'){
            $key = 'wa_lock';
            $value = 0;
            $valueTo = 1;
        }else{
            return $this->showResult(304,'非法请求');
        }
        if(in_array($button,['merchant_down','merchant_up'])){
            $table = 'merchant_info';
            $models = MerchantInfo::find()->where("$key=$value and id in $ids")->one();
        }else{
            $table = 'wine_admin';
            $models = Admin::find()->where("$key=$value and wa_id in (SELECT wa_id FROM merchant_info WHERE id IN $ids)")->one();
        }
        if(!empty($models)){
            $sql = "UPDATE $table SET $key = $valueTo";
            if($key == 'is_active'){
                $sql .= " ,active_at=".time();
                $sql .= " WHERE id IN $ids AND $key=$value";
            }else{
                $sql.= " ,updated_time='".date('Y-m-d H:i:s')."'";
                $sql .= " WHERE wa_id in (SELECT wa_id FROM merchant_info WHERE id IN $ids)";
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

    /*
     * 查找数据
     */
    protected function findModel($id)
    {
        $model = MerchantInfo::find()->joinWith('wa')->addSelect(['merchant_info.*','wine_admin.wa_username as wa_username','concat("*****") as wa_password','wa_logo'])->where("id=$id")->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



}
