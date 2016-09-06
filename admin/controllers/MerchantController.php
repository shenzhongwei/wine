<?php

namespace admin\controllers;

use admin\models\Admin;
use admin\models\MerchantInfoSearch;
use admin\models\UploadForm;
use admin\models\Zone;
use Yii;
use admin\models\MerchantInfo;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
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

    /**
     * Creates a new MerchantInfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
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

        $logourl='';
        $model = new MerchantInfo;

        if (Yii::$app->request->post()) {
            //获取传过来的值
            $merchant=Yii::$app->request->post('MerchantInfo');

            if(empty($merchant['name']) ){
                return $this->showResult(400,'请输入商户名称');
            }
            if(empty($merchant['wa_username']) ){
                return $this->showResult(400,'请输入后台登陆名');
            }
            $exist_model = Admin::findIdentityByUsername($merchant['wa_username']);
            if(!empty($exist_model)){
                return $this->showResult(400,'该后台登陆名已被使用');
            }
            if(empty($merchant['wa_password'])){
                return $this->showResult(400,'请输入密码');
            }
            if(strlen($merchant['wa_password'])>16 ||strlen($merchant['wa_password'])<5){
                $message = '密码长度为5-16位';
                return $this->showResult(400,$message);
            }
            if(!$this->validateMobilePhone($merchant['phone'])&&!empty($merchant['phone'])){
                $message = '手机格式错误';
                return $this->showResult(400,$message);
            }

            //上传头像
            $img =UploadedFile::getInstance($model,'wa_logo');
            $pic_path = '../../photo/logo/';
            $img_temp='/logo/';
            $logourl=SiteController::actionUpload($user_id,$img,$pic_path,$img_temp);
//            if(!empty($img)){
//               $ext = $img->getExtension();
//                $pic_path = '../../photo/logo/';
//                if(!is_dir($pic_path)){
//                    @mkdir($pic_path,0777,true);
//                }
//                $logo_name = 'admin_'.time().$user_id.rand(100,999).'.'.$ext;
//                $res=$img->saveAs($pic_path.$logo_name);//设置图片的存储位置
//                $logourl='/logo/'.$logo_name;
//            }
            //var_dump($merchant);exit;
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

                $p=Zone::getDetailName($merchant['province']);;
                $c=Zone::getDetailName($merchant['city']);
                $d=Zone::getDetailName($merchant['district']);
                //创建商户信息
                $model->attributes=[
                    'name'=>$merchant['name'],
                    'wa_id'=>$admin->wa_id,
                    'region'=>$merchant['region'],
                    'address'=>$merchant['address'],
                    'phone'=>$merchant['phone'],
                    'registe_at'=>date('YmdHis'),
                    'active_at'=>date('YmdHis'),
                    'province'=>$p,
                    'city'=>$c,
                    'district'=>$d,
                ];
                if(!$model->save()){
                    throw new Exception;
                }
                $user_id = $model->wa_id;
                $role = $auth->createRole('商家管理员');      //创建角色对象
                $auth->assign($role, $user_id);                           //添加对应关系

                $transaction->commit();//提交
                Yii::$app->session->setFlash('success','商户添加成功');
                return $this->redirect(['view', 'id' => $model->id]);
              }catch(Exception $e){
                $transaction->rollBack();
                Yii::$app->session->setFlash('danger','商户添加失败');
                return $this->redirect('create',[
                    'model' => $model,
                    'item_arr'=>$itemArr
                ]);
              }
        } else {
            //跳到 新建 页面
            $model->wa_type='3';
            return $this->render('create', [
                'model' => $model,
                'item_arr'=>$itemArr
            ]);
        }

    }

    /**
     * Updates an existing MerchantInfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->post()) {
            $merchant=Yii::$app->request->post('MerchantInfo');
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $p=Zone::getDetailName($merchant['province']);;
                $c=Zone::getDetailName($merchant['city']);
                $d=Zone::getDetailName($merchant['district']);
                //创建商户信息
                $model->attributes=[
                    'name'=>$merchant['name'],
                    'region'=>$merchant['region'],
                    'address'=>$merchant['address'],
                    'phone'=>$merchant['phone'],
                    'registe_at'=>date('YmdHis'),
                    'active_at'=>date('YmdHis'),
                    'province'=>$p,
                    'city'=>$c,
                    'district'=>$d,
                ];
                if(!$model->save()){
                    throw new Exception;
                }
                $transaction->commit();//提交
                Yii::$app->session->setFlash('success','商户修改成功');
                return $this->redirect(['view', 'id' => $model->id]);
            }catch(Exception $e){
                $transaction->rollBack();
                Yii::$app->session->setFlash('danger','商户修改失败');
                return $this->redirect('create',[
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
     * Deletes an existing MerchantInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
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
        }
        if($merchantInfo->save()){
            Yii::$app->session->setFlash('success','修改成功');
        }else{
            Yii::$app->session->setFlash('danger','失败，请重试');
        }
        return $this->redirect(['index']);


    }

    /**
     * Finds the MerchantInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MerchantInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
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
