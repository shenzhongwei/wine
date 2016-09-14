<?php

namespace admin\controllers;

use admin\models\Admin;
use admin\models\MerchantInfoSearch;
use admin\models\UploadForm;
use admin\models\Zone;
use kartik\form\ActiveForm;
use Yii;
use admin\models\MerchantInfo;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
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

        //获取所有的商户名称
        $mername=MerchantInfoSearch::getAllMerchant();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'mername'=>$mername
        ]);
    }

    /**
     * Displays a single MerchantInfo model.
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
        if(!empty($id)){
            $model = new MerchantInfo();
        }else{
            $model = new MerchantInfo(['scenario'=>'create']);
        }
        $model->load($data);
        return ActiveForm::validate($model);
    }


    /*
     * 创建
     */
    public function actionCreate()
    {
        $user_id = Yii::$app->user->identity->getId();
        if(empty($user_id)){
            return $this->showResult(302,'用户信息获取失败');
        }
        $auth = Yii::$app->authManager;
        $item = $auth->getRolesByType(Yii::$app->user->identity->wa_type);
        $itemArr = ArrayHelper::map($item,'level','name');

        $model = new MerchantInfo(['scenario'=>'create']);
        if (Yii::$app->request->post()&& $model->load(Yii::$app->request->post()) && $model->validate()) {
            //获取传过来的值
            $merchant=Yii::$app->request->post('MerchantInfo');

            //上传头像
            $img =UploadedFile::getInstance($model,'wa_logo');
            $pic_path = '../../photo/logo/';
            $img_temp='/logo/';
            $logourl=SiteController::actionUpload($user_id,$img,$pic_path,$img_temp);

            $transaction = Yii::$app->db->beginTransaction();
            try{
                //创建后台商户管理员
                $admin=new Admin();
                $admin->attributes=[
                    'wa_username'=>$merchant['wa_username'],
                    'wa_password'=>md5(strtolower($merchant['wa_password'])),
                    'wa_type'=>3,
                    'wa_name'=>$merchant['name'],
                    'wa_token'=>Yii::$app->getSecurity()->generateRandomString(),
                    'wa_logo'=>$logourl,
                    'created_time'=>date('Y-m-d H:i:s'),
                    'updated_time'=>date('Y-m-d H:i:s')
                ];
                if(!$admin->save()){
                    throw new Exception;
                }
                //获取省-市-区
                $p=Zone::getDetailName($merchant['province']);
                $c=$d='';
                if(isset($merchant['city'])){
                    $c=Zone::getDetailName($merchant['city']);
                }
                if(isset($merchant['district'])){
                    $d=Zone::getDetailName($merchant['district']);
                }
                //创建商户信息
                $model->attributes=[
                    'name'=>$merchant['name'],
                    'wa_id'=>$admin->wa_id,
                    'region'=>$merchant['region'],
                    'address'=>$merchant['address'],
                    'phone'=>$merchant['phone'],
                    'registe_at'=>time(),
                    'active_at'=>time(),
                    'province'=>$p,
                    'city'=>$c,
                    'district'=>$d,
                    'lng'=>empty($d)?(empty($c)?(empty($p)?'':Zone::getLngLat($c)['lng']*1000000):Zone::getLngLat($p)['lng']*1000000):Zone::getLngLat($d)['lng']*1000000,
                    'lat'=>empty($d)?(empty($c)?(empty($p)?'':Zone::getLngLat($c)['lat']*1000000):Zone::getLngLat($p)['lat']*1000000):Zone::getLngLat($d)['lat']*1000000,
                ];
                if(!$model->save()){
                    throw new Exception;
                }
                //创建角色对象
                $user_id = $model->wa_id;
                $role = $auth->createRole('商家管理员');      //创建角色对象
                $auth->assign($role, $user_id);                           //添加对应关系

                $transaction->commit();//提交
                return $this->redirect(['view', 'id' => $model->id]);
              }catch(Exception $e){
                $transaction->rollBack();
              }
        } else {
            //跳到 新建 页面
            $model->wa_type='3';
            return $this->render('create', [
                'model' => $model,
                'item_arr'=>$itemArr,
                'province'=>ArrayHelper::map(Zone::getProvince(),'id','name'),
                'city'=>[],
                'district'=>[],
            ]);
        }

    }

    /*
     * 更新
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->post()) {
            $merchant=Yii::$app->request->post('MerchantInfo');
           // var_dump($merchant);exit;
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $p=Zone::getDetailName($merchant['province']);
                $c=$d='';
                if(isset($merchant['city'])){
                    $c=Zone::getDetailName($merchant['city']);
                }
                if(isset($merchant['district'])){
                    $d=Zone::getDetailName($merchant['district']);
                }

                //更新商户信息
                $model->attributes=[
                    'name'=>$merchant['name'],
                    'region'=>$merchant['region'],
                    'address'=>$merchant['address'],
                    'phone'=>$merchant['phone'],
                    'province'=>empty($p)?$model->province:$p,
                    'city'=>empty($c)?$model->city:$c,
                    'district'=>empty($d)?$model->district:$d,
                ];
                if(!$model->save()){
                    throw new Exception;
                }
                //保存经纬度
                $model->attributes=[
                    'lng'=>empty($model->district)?(empty($model->city)?(empty($model->province)?'':Zone::getLngLat($model->city)['lng']*1000000):Zone::getLngLat($model->province)['lng']*1000000):Zone::getLngLat($model->district)['lng']*1000000,
                    'lat'=>empty($model->district)?(empty($model->city)?(empty($model->province)?'':Zone::getLngLat($model->city)['lat']*1000000):Zone::getLngLat($model->province)['lat']*1000000):Zone::getLngLat($model->district)['lat']*1000000,
                ];
                if(!$model->save()){
                    throw new Exception;
                }
                $transaction->commit();//提交

                return $this->redirect(['view', 'id' => $model->id]);
            }catch(Exception $e){
                $transaction->rollBack();
            }
        } else {
            $model->province=empty($model->province)?'':Zone::getDetailId($model->province);
            $model->city=empty($model->city)?'':Zone::getDetailId($model->city);
            $model->district=empty($model->district)?'':Zone::getDetailId($model->district);

            return $this->render('update', [
                'model' => $model,
                'province'=>ArrayHelper::map(Zone::getProvince(),'id','name'),
                'city'=>ArrayHelper::map(Zone::getCity($model->province),'id','name'),
                'district'=>ArrayHelper::map(Zone::getDistrict( $model->city),'id','name')
            ]);
        }
    }

    /*
     * 删除
     */
    public function actionDelete()
    {
        $user_id = Yii::$app->user->identity->getId();
        if(empty($user_id)){
            return $this->showResult(302,'用户登录信息失效');
        }
        $id=Yii::$app->request->get('id');
        if(empty($id)){
            return $this->showResult(301,'读取数据发生错误');
        }
        $merchantInfo =MerchantInfo::findOne([$id]);
        if(empty($merchantInfo)){
            return $this->showResult(301,'未获取到该商户的信息');
        }
        $merchantInfo->active_at = date('YmdHis');
        if($merchantInfo->is_active==1){
            $merchantInfo->is_active=0;
        }else{
            $merchantInfo->is_active=1;
            $merchantInfo->active_at=time();
        }
        if($merchantInfo->save()){
            Yii::$app->session->setFlash('success','修改成功');
        }else{
            Yii::$app->session->setFlash('danger','失败，请重试');
        }
        return $this->redirect(['index']);


    }

    /*
     * 查找数据
     */
    protected function findModel($id)
    {
        if (($model = MerchantInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



}
