<?php

namespace admin\controllers;

use admin\models\Admin;
use admin\models\MerchantInfo;
use admin\models\MerchantInfoSearch;
use admin\models\Zone;
use Yii;
use admin\models\ShopInfo;
use admin\models\ShopSearch;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
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

        //获取所有的商户名称
        $mername=MerchantInfoSearch::getAllMerchant();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'mername'=>$mername
        ]);
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

    /**
     * Creates a new ShopInfo model.
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
        $itemArr =ArrayHelper::map($item,'level','name');

        $logourl='';
        $model = new ShopInfo;

        if (Yii::$app->request->post()) {
            //获取传过来的值
            $shop=Yii::$app->request->post('ShopInfo');

            if(empty($shop['name']) ){
                return $this->showResult(400,'请输入门店名称');
            }
            if(empty($shop['wa_username']) ){
                return $this->showResult(400,'请输入后台登陆名');
            }
            $exist_model = Admin::findIdentityByUsername($shop['wa_username']);
            if(!empty($exist_model)){
                return $this->showResult(400,'该后台登陆名已被使用');
            }
            if(empty($shop['wa_password'])){
                return $this->showResult(400,'请输入密码');
            }
            if(strlen($shop['wa_password'])>16 ||strlen($shop['wa_password'])<5){
                $message = '密码长度为5-16位';
                return $this->showResult(400,$message);
            }


            //上传头像
            $img =UploadedFile::getInstance($model,'wa_logo');
            $pic_path = '../../photo/logo/';
            $img_temp='/logo/';
            $logourl=SiteController::actionUpload($user_id,$img,$pic_path,$img_temp);

            //上传营业执照
            $img =UploadedFile::getInstance($model,'bus_pic');
            $pic_path = '../../photo/business/';
            $img_temp='/business/';
            $businessurl=SiteController::actionUpload($user_id,$img,$pic_path,$img_temp);

            //上传门店logo
            $img =UploadedFile::getInstance($model,'logo');
            $pic_path = '../../photo/shop/';
            $img_temp='/shop/';
            $shopurl=SiteController::actionUpload($user_id,$img,$pic_path,$img_temp);

            $transaction = Yii::$app->db->beginTransaction();
            try{
                //创建后台商户管理员
                $admin=new Admin();
                $admin->attributes=[
                    'wa_username'=>$shop['wa_username'],
                    'wa_password'=>md5(strtolower($shop['wa_password'])),
                    'wa_type'=>4,
                    'wa_name'=>$shop['wa_username'],
                    'wa_token'=>Yii::$app->getSecurity()->generateRandomString(),
                    'wa_logo'=>$logourl,
                    'created_time'=>date('Y-m-d H:i:s'),
                    'updated_time'=>date('Y-m-d H:i:s')
                ];
                if(!$admin->save()){
                    throw new Exception;
                }

                $p=Zone::getDetailName($shop['province']);;
                $c=Zone::getDetailName($shop['city']);
                $d=Zone::getDetailName($shop['district']);
                //创建门店信息
                $model->attributes=[
                    'name'=>$shop['name'],
                    'wa_id'=>$admin->wa_id,
                    'merchant'=>$shop['merchant'],
                    'region'=>$shop['region'],
                    'address'=>$shop['address'],
                    'limit'=>$shop['limit'],
                    'least_money'=>$shop['least_money'],
                    'send_bill'=>$shop['send_bill'],
                    'no_send_need'=>$shop['no_send_need'],
                    'bus_pic'=>$businessurl,
                    'logo'=>$shopurl,
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
                $role = $auth->createRole('门店管理员');      //创建角色对象
                $auth->assign($role, $user_id);                           //添加对应关系

                $transaction->commit();//提交
                Yii::$app->session->setFlash('success','门店添加成功');
                return $this->redirect(['view', 'id' => $model->id]);
            }catch(Exception $e){
                $transaction->rollBack();
                Yii::$app->session->setFlash('danger','门店添加失败');
                return $this->redirect('create',[
                    'model' => $model,
                    'item_arr'=>$itemArr
                ]);
            }
        } else {
            //跳到 新建 页面
            $model->wa_type='4';
            return $this->render('create', [
                'model' => $model,
                'item_arr'=>$itemArr
            ]);
        }
    }

    /**
     * Updates an existing ShopInfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ShopInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ShopInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ShopInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ShopInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
