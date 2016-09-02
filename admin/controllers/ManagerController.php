<?php
namespace admin\controllers;


use admin\models\Admin;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: 沈小鱼
 * Date: 2016/7/26
 * Time: 19:37
 */
 class ManagerController extends BaseController{



     public function behaviors()
     {
         return [
             'access' => [
                 'class' => AccessControl::className(),
                 'rules' => [
                     [
                         'actions' => ['index','update','upload','list','create','delete','lock','del','recover','search','searchlist'],
                         'allow' => true,
                         'roles' => ['@'],
                     ],
                 ],
             ],
             'verbs' => [
                 'class' => VerbFilter::className(),
                 'actions' => [
                     'upload' => ['post'],
                 ],
             ],
         ];
     }


     public function actionIndex(){
         $logo = empty(Yii::$app->user->identity->wa_logo) ? '':Yii::$app->params['img_path'].Yii::$app->user->identity->wa_logo;
         return $this->render('logo',[
             'logo'=>$logo,
         ]);
     }

     //用户列表
     public function actionList()
     {
         $searchArr = Yii::$app->request->get('search');
         $data = Admin::find()->where('wa_type>='.Yii::$app->user->identity->wa_type);
         if(!empty($searchArr)){
             foreach($searchArr as $key=>$val){
                 $data->andFilterWhere(['like',$key,$val]);
             }
         }
         $pages = new Pagination([
             'totalCount' =>$data->count(),
             'pageSize' => '15',
             'pageSizeParam' => false,
         ]);
         $admin = $data->joinWith('admingroup')->offset($pages->offset)->limit($pages->limit)->all();
         $auth = Yii::$app->authManager;
         $item = $auth->getRolesByType(Yii::$app->user->identity->wa_type);
         $itemArr = ArrayHelper::map($item,'level','name');
         if(empty($searchArr)){
             return $this->render('list',[
                 'admin'=>$admin,
                 'pages' => $pages,
                 'itemArr'=>$itemArr,
                 'searchArr'=>$searchArr,
             ]);
         }else{
             return $this->renderPartial('list',[
                 'admin'=>$admin,
                 'pages' => $pages,
                 'itemArr'=>$itemArr,
                 'searchArr'=>$searchArr,
             ]);
         }

     }

     /**
      * @return array
      * 上传图像
      */
     public function actionUpload(){
         $user_id = Yii::$app->user->identity->getId();
         if(empty($user_id)){
             return $this->showResult(302,'用户登录信息失效');
         }
         $imgtypes=[
             'image/jpg',
             'image/jpeg',
             'image/png',
             'image/pjpeg',
             'image/gif',
             'image/bmp',
             'image/x-png'
         ];
         $max_file_size=2048000;
         $pic_data = Yii::$app->request->post('mypic');
         if(empty($pic_data)){
             return $this->showResult(301,'未获取到图片数据');
         }
         $pos1 = strpos($pic_data,'data:');
         $pos2 = strpos($pic_data,';base64');
         if($pos1===false || $pos2===false){
             return $this->showResult(301,'未获取到图片数据');
         }else{
             $type = substr($pic_data,$pos1+5,$pos2-$pos1-5);
             if(!in_array($type,$imgtypes)){
                 return $this->showResult(301,'图片格式错误');
             }
             $pic_content = substr($pic_data,$pos2+8);
             if(empty($pic_content)){
                 return $this->showResult(301,'未获取到图片数据');
             }
             $pic =base64_decode($pic_content );
             if(empty($pic)){
                 return $this->showResult(301,'未获取到图片数据');
             }
             $logoPath = Yii::$app->params['img_path'].'/logo/';
             if(!is_dir($logoPath)){
                 @mkdir($logoPath,0777,true);
             }
             $logo_name = 'admin_'.time().$user_id.rand(100,999).'.'.substr($type,6);
             $pic_path = $logoPath.$logo_name;
             if(file_put_contents($pic_path,$pic)){
                 $size = filesize($pic_path);
                 if($size > $max_file_size){
                     @unlink ($pic_path);
                     return $this->showResult(301,'图片大小不能超过2M');
                 }
                 $admin = Admin::findOne(['wa_id'=>$user_id]);
                 if(empty($admin)){
                     return $this->showResult(302,'用户登录信息失效');
                 }else{
                     $admin->wa_logo = '/logo/'.$logo_name;
                     if(!$admin->save()){
                         return $this->showResult(400,'保存失败，请重试');
                     }else{
                         return $this->showResult(200,'修改头像成功',$pic_path);
                     }
                 }
             }else{
                 return $this->showResult(301,'上传失败');
             }
         }
     }

     public function actionUpdate(){
         $user_id = Yii::$app->user->identity->getId();
         if(empty($user_id)){
             return $this->showResult(302,'用户登录信息失效');
         }
         $data = Yii::$app->request->post();
         if(empty($data)){
             $id = Yii::$app->request->get('id');
         }else{
             $id = $data['wa_id'];
         }
         $model = Admin::find()->joinWith('admingroup')->where(['wa_id'=>$id])->one();
         $auth = Yii::$app->authManager;
         $item = $auth->getRolesByType(Yii::$app->user->identity->wa_type);
         $itemArr =[];
         foreach($item as $v){
             $itemArr[$v->name] = $v->name;
         }
         if(!empty($data)){
             if(strlen($data['wa_password'])>16 ||strlen($data['wa_password'])<5){
                 $message = '密码长度为5-16位';
                 return $this->showResult(400,$message);
             }
             if(str_replace('*','',$data['wa_password']) != str_replace('*','',$data['confirm_password'])){
                 $message = '两次密码输入不一致';
                 return $this->showResult(400,$message);
             }
             $data['wa_username'] = empty($data['wa_username']) ? $model->wa_username:$data['wa_username'];
             $data['wa_password'] = (empty(str_replace('*','',$data['wa_password']))) ? $model->wa_password:md5(Yii::$app->params['pwd_pre'].$data['wa_password']);
             $data['wa_phone'] = empty($data['wa_phone']) ? '':$data['wa_phone'];
             $data['wa_name'] = empty($data['wa_name']) ? '':$data['wa_name'];
             $data['item_name'] = empty($data['item_name']) ? $model->admingroup->item_name:$data['item_name'];
             if($data['wa_username']!=$model->wa_username){
                 $message = '用户名不可修改';
                 return $this->showResult(400,$message);
             }
             if(!$this->validateMobilePhone($data['wa_phone'])&&!empty($data['wa_phone'])){
                 $message = '手机格式错误';
                 return $this->showResult(400,$message);
             }
             if(!in_array($data['item_name'],$itemArr)){
                 $message = '用户组不可用';
                 return $this->showResult(400,$message);
             }
             if($model->wa_password != $data['wa_password']&&$user_id==$data['wa_id']){
                 $password_changed = 1;
             }else{
                 $password_changed = 0;
             }
             $model->wa_password = $data['wa_password'];
             $model->wa_username = $data['wa_username'];
             $model->wa_phone = $data['wa_phone'];
             $model->wa_name = $data['wa_name'];
             $role = $auth->createRole($data['item_name']);                //创建角色对象
             $auth->revokeAll($data['wa_id']);
             $auth->assign($role, $data['wa_id']);                           //添加对应关系
             if($model->save()){
                 if($password_changed==1){
                     Yii::$app->session->destroy();
                     return $this->showResult(304,'您的密码已被修改，请重新登录');
                 }else{
                     return $this->showResult(200,'修改成功');
                 }
             }else{
                 return $this->showResult(400,'修改失败，请重试');
             }
         }else{
             return $this->render('update',[
                 'model' => $model,
                 'item' => $itemArr
             ]);
         }
     }

     //新增用户
     public function actionCreate(){
         $user_id = Yii::$app->user->identity->getId();
         if(empty($user_id)){
             return $this->showResult(302,'用户登录信息失效');
         }
         $model = new Admin();
         $auth = Yii::$app->authManager;
         $item = $auth->getRolesByType(Yii::$app->user->identity->wa_type);
         $itemArr =[];
         foreach($item as $v){
             $itemArr[$v->name] = $v->name;
         }
         $data = Yii::$app->request->post();
         if(!empty($data)){
             if(empty($data['wa_username'])){
                 return $this->showResult(400,'请输入用户名');
             }
             $exist_model = Admin::findIdentityByUsername($data['wa_username']);
             if(!empty($exist_model)){
                 return $this->showResult(400,'该用户名已被使用');
             }
             if(empty($data['wa_password'])||empty($data['confirm_password'])){
                 return $this->showResult(400,'请输入密码');
             }
             if(strlen($data['wa_password'])>16 ||strlen($data['wa_password'])<5){
                 $message = '密码长度为5-16位';
                 return $this->showResult(400,$message);
             }
             if(!$this->validateMobilePhone($data['wa_phone'])&&!empty($data['wa_phone'])){
                 $message = '手机格式错误';
                 return $this->showResult(400,$message);
             }
             if(!in_array($data['item_name'],$itemArr)){
                 $message = '用户组不可用';
                 return $this->showResult(400,$message);
             }
             $model->attributes = [
                 'wa_username'=>$data['wa_username'],
                 'wa_password'=>md5(Yii::$app->params['pwd_pre'].$data['wa_password']),
                 'wa_type'=>$data['item_name']=='超级管理员' ? 1:2,
                 'wa_phone'=>$data['wa_phone'],
                 'wa_name'=>$data['wa_name'],
                 'wa_token'=>Yii::$app->security->generateRandomString(),
                 'wa_logo'=>'',
                 'wa_last_login_time'=>'1999-01-01 01:01:01',
                 'wa_last_login_ip'=>'',
                 'wa_lock'=>0,
                 'wa_status'=>1,
                 'created_time'=>date('Y-m-d H:i:s',time()),
                 'updated_time'=>date('Y-m-d H:i:s',time()),
             ];
             if(!$model->save()){
                 return $this->showResult(400,'增加管理员失败，请重试');
             }else{
                 $user_id = $model->wa_id;
                 $role = $auth->createRole($data['item_name']);                //创建角色对象
                 $auth->assign($role, $user_id);                           //添加对应关系
                 return $this->showResult(200,'增加管理员成功');
             }
         }else{
             return $this->render('create', [
                 'model' => $model,
                 'item' => $itemArr
             ]);
         }
     }

     public function actionLock(){
         $user_id = Yii::$app->user->identity->getId();
         if(empty($user_id)){
             return $this->showResult(302,'用户登录信息失效');
         }
         $wa_id = Yii::$app->request->post('wa_id');
         if(empty($wa_id)){
             return $this->showResult(301,'读取数据发生错误');
         }
         $adminInfo = Admin::findOne(['wa_id'=>$wa_id]);
         if(empty($adminInfo)){
             return $this->showResult(301,'未获取到该用户的信息');
         }
         $adminInfo->updated_time = date('Y-m-d H:i:s',time());
         if($adminInfo->wa_lock==1){
             $adminInfo->wa_lock=0;
         }else{
             $adminInfo->wa_lock=1;
         }
         if($adminInfo->save()){
             return $this->showResult(200,'修改成功');
         }else{
             return $this->showResult(400,'修改失败，请重试');
         }
     }

     public function actionDel(){
         $user_id = Yii::$app->user->identity->getId();
         if(empty($user_id)){
             return $this->showResult(302,'用户登录信息失效');
         }
         $wa_id = Yii::$app->request->post('wa_id');
         if(empty($wa_id)){
             return $this->showResult(301,'读取数据发生错误');
         }
         $adminInfo = Admin::findOne(['wa_id'=>$wa_id]);
         if(empty($adminInfo)){
             return $this->showResult(301,'未获取到该用户的信息');
         }
         $adminInfo->wa_status=0;
         $adminInfo->updated_time = date('Y-m-d H:i:s',time());
         if($adminInfo->save()){
             return $this->showResult(200,'删除成功');
         }else{
             return $this->showResult(400,'删除失败，请重试');
         }
     }

     public function actionRecover(){
         $user_id = Yii::$app->user->identity->getId();
         if(empty($user_id)){
             return $this->showResult(302,'用户登录信息失效');
         }
         $wa_id = Yii::$app->request->post('wa_id');
         if(empty($wa_id)){
             return $this->showResult(301,'读取数据发生错误');
         }
         $adminInfo = Admin::findOne(['wa_id'=>$wa_id]);
         if(empty($adminInfo)){
             return $this->showResult(301,'未获取到该用户的信息');
         }
         $adminInfo->wa_status=1;
         $adminInfo->updated_time = date('Y-m-d H:i:s',time());
         if($adminInfo->save()){
             return $this->showResult(200,'恢复成功');
         }else{
             return $this->showResult(400,'恢复失败，请重试');
         }
     }

     public function actionSearch(){
         $user = Yii::$app->user->identity;
         if(empty($user)){
             $this->goHome();
         }else{
             $key = Yii::$app->request->get('key');
             $val = Yii::$app->request->get('val');
             $values = Admin::find()->select($key)->where($key." like '%".$val."%' and wa_type>=".$user->wa_type)->asArray()->all();
             if(empty($values)){
                 $data = [];
             }else{
                 $data = array_unique(array_column($values,$key));
             }
             return $this->showResult(200,'成功',$data);
         }
     }
     protected function findModel($id)
     {
         if (($model = Admin::findOne($id)) !== null) {
             return $model;
         } else {
             throw new NotFoundHttpException('The requested page does not exist.');
         }
     }
}