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
use yii\helpers\Url;
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


    public function actionCreate()
    {
        $user_id = Yii::$app->user->identity->getId();
        if(empty($user_id)){
            return $this->showResult(302,'用户信息获取失败');
        }
        $auth = Yii::$app->authManager;
        $item = $auth->getRolesByType(Yii::$app->user->identity->wa_type);
        $itemArr =ArrayHelper::map($item,'level','name');

        $model = new ShopInfo;
        $p1 = $p2='';
        $P= [];

        if (Yii::$app->request->post()) {
            //获取传过来的值
            $shop=Yii::$app->request->post('ShopInfo');
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

                $p=Zone::getDetailName($shop['province']);
                $c=$d='';
                if(isset($shop['city'])){
                    $c=Zone::getDetailName($shop['city']);
                }
                if(isset($shop['district'])){
                    $d=Zone::getDetailName($shop['district']);
                }

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
                    'no_send_need'=>empty($shop['no_send_need'])?0:$shop['no_send_need'],
                    'bus_pic'=>$businessurl,
                    'logo'=>$shopurl,
                    'registe_at'=>time(),
                    'active_at'=>time(),
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
                $model->wa_type='4';
                return $this->redirect('create',[
                    'model' => $model,
                    'item_arr'=>$itemArr,
                    'p1'=>$p1,'p2'=>$p2,
                    'PreviewConfig' =>$P,
                ]);
            }
        } else {
            //跳到 新建 页面
            $model->wa_type='4';
            return $this->render('create', [
                'model' => $model,
                'item_arr'=>$itemArr,
                'p1'=>$p1,'p2'=>$p2,
                'PreviewConfig' =>$P,
            ]);
        }
    }


    public function actionUpdate($id)
    {
        $auth = Yii::$app->authManager;
        $item = $auth->getRolesByType(Yii::$app->user->identity->wa_type);
        $itemArr =ArrayHelper::map($item,'level','name');

        $model = $this->findModel($id);
        // 对门店图进行处理
        $p1 = $p2='';$P= [];
        if ($model) {
            $p1 = Yii::$app->params['img_path'].$model->bus_pic;
            $p2 =Yii::$app->params['img_path'].$model->logo;
            $P = [
                'url' =>Url::toRoute('/shop/delete'),
                'key' => $model->id,
                'width'=>'200px'
            ];
        }

        if (Yii::$app->request->post()) {
            //获取传过来的值
            $shop=Yii::$app->request->post('ShopInfo');
            $user_id = Yii::$app->user->identity->getId();
            if(empty($user_id)){
                return $this->showResult(302,'用户信息获取失败');
            }
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

                $p=Zone::getDetailName($shop['province']);
                $c=$d='';
                if(isset($shop['city'])){
                    $c=Zone::getDetailName($shop['city']);
                }
                if(isset($shop['district'])){
                    $d=Zone::getDetailName($shop['district']);
                }

                //创建门店信息
                $model->attributes=[
                    'name'=>$shop['name'],
                    'region'=>$shop['region'],
                    'address'=>$shop['address'],
                    'limit'=>$shop['limit'],
                    'least_money'=>$shop['least_money'],
                    'send_bill'=>$shop['send_bill'],
                    'no_send_need'=>empty($shop['no_send_need'])?0:$shop['no_send_need'],
                    'bus_pic'=>empty($businessurl)?$shop['bus_pic_url']:$businessurl,
                    'logo'=>empty($shopurl)?$shop['logo_url']:$shopurl,
                    'registe_at'=>time(),
                    'active_at'=>time(),
                    'province'=>empty($p)?$model->province:$p,
                    'city'=>empty($c)?$model->city:$c,
                    'district'=>empty($d)?$model->district:$d,
                ];
                if(!$model->save()){
                    throw new Exception;
                }

                $transaction->commit();//提交
                Yii::$app->session->setFlash('success','门店添加成功');
                return $this->redirect(['view', 'id' => $model->id]);
            }catch(Exception $e){
                $transaction->rollBack();
                Yii::$app->session->setFlash('danger','门店添加失败');
                $model->wa_type='4';
                return $this->redirect('update',[
                    'model' => $model,
                    'p1'=>$p1,'p2'=>$p2,
                    'PreviewConfig' =>$P,
                ]);
            }
        } else {

            return $this->render('update', [
                'model' => $model,
                'p1'=>$p1,'p2'=>$p2,
                'PreviewConfig' =>$P,
            ]);
        }
    }


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
        $shopInfo =ShopInfo::findOne([$id]);
        if(empty($shopInfo)){
            return $this->showResult(301,'未获取到该门店的信息');
        }
        $shopInfo->active_at = date('YmdHis');
        if($shopInfo->is_active==1){ //正常状态
            $shopInfo->is_active=0;
        }else{  //失效状态
            $shopInfo->is_active=1;
            $shopInfo->active_at=time();
        }
        if($shopInfo->save()){
            Yii::$app->session->setFlash('success','修改成功');
        }else{
            Yii::$app->session->setFlash('danger','失败，请重试');
        }
        return $this->redirect(['index']);


    }


    protected function findModel($id)
    {
        if (($model = ShopInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
