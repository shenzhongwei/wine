<?php
namespace api\controllers;

use api\models\MessageList;
use api\models\UserInfo;
use api\models\UserLogin;
use Yii;
use \Exception;
use yii\web\UploadedFile;

/**
 * Created by PhpStorm.
 * User: me
 * Date: 2016/8/10
 * Time: 15:06
 */

class UserController extends ApiController{

    /**
     * 判断用户是否存在接口
     */
    public function actionIsExist(){
        //获取手机号
        $phone = Yii::$app->request->post('phone','');
        if(empty($phone)){
            return $this->showResult(301,'未获取到您的手机号');
        }
        $isExist = UserLogin::findIdentityByUsername($phone);
        if(!empty($isExist)){
            $isExist = '1';
        }else{
            $isExist = '0';
        }
        $result  =[
            'isExist'=>$isExist,
        ];
        return $this->showResult(200,'成功',$result);
    }

    /**
     * 发送验证码接口
     */
    public function actionSendMessage(){
        //获取手机号
        $phone = Yii::$app->request->post('phone','');
        $code = rand(111111,999999);
        Yii::$app->cache->set('message_'.$phone,$code);
        return $this->showResult(200,'发送成功',$code);
    }

    /**
     * 注册接口
     */
    public function actionRegister(){
        //获取表单数据
        $phone = Yii::$app->request->post('phone','');
        $code = Yii::$app->request->post('code','');
        $password = Yii::$app->request->post('password','');
        $confirmPwd = Yii::$app->request->post('confirmPwd','');
        $inviteCode = Yii::$app->request->post('inviteCode','');
        //判断数据是否完整获取
        if(empty($phone)){
            return $this->showResult(301,'未获取到您的手机号');
        }
        if(empty($password)||empty($confirmPwd)){
            return $this->showResult(301,'密码选项不能为空');
        }
        if(empty($code)){
            return $this->showResult(301,'未获取到您的验证码');
        }
        if($password!==$confirmPwd){
            return $this->showResult(301,'两次输入的密码不一致');
        }
        //判断手机号格式
        if(!$this->validateMobilePhone($phone)){
            return $this->showResult(303,'手机格式错误');
        }
        //判断是否已注册
        $isExist = UserLogin::findIdentityByUsername($phone);
        if(!empty($isExist)){
            return $this->showResult(303,'该用户已注册，请勿重复注册');
        }
        //判断验证码是否正确
        $codeCache = Yii::$app->cache->get('message_'.$phone);
        if($codeCache===false){
            return $this->showResult(303,'验证码已过期，请重新获取');
        }elseif($codeCache!=$code){
            return $this->showResult(303,'验证码错误，请重新输入');
        }
        //判断邀请码是否存在
        if(!empty($inviteCode)){
            $invitedUser = UserInfo::getInfoByInviteCode($inviteCode);
            if(empty($invitedUser)){
                return $this->showResult(303,'邀请码错误，请重新输入');
            }elseif($invitedUser->status==0){
                return $this->showResult(303,'该邀请码账户状态异常，使用邀请码失败');
            }
        }
        //开始数据库操作
        //开启事务
        $transaction = Yii::$app->db->beginTransaction();
        try{
            //存用户信息
            $userInfo = new UserInfo();
            $userInfo->attributes = [
                'phone'=>$phone,
                'nickname'=>$phone,
                'realname'=>$phone,
                'invite_user_id'=>empty($invitedUser) ? 0:$invitedUser->id,
                'is_vip'=>0,
                'status'=>1,
                'created_time'=>date('Y-m-d H:i:s'),
                'updated_time'=>date('Y-m-d H:i:s'),
            ];
            if(!$userInfo->save()){
                throw new Exception('生成用户信息出错');
            }

            //存登录信息
            $userLogin = new UserLogin();
            $userLogin->attributes = [
                'token'=>Yii::$app->security->generateRandomString(),
                'uid'=>$userInfo->id,
                'username'=>$phone,
                'password'=>md5(Yii::$app->params['pwd_pre'].$password),
                'status'=>$userInfo->status,
            ];
            if(!$userLogin->save()){
                throw new Exception('生成登陆信息出错');
            }
            //生成邀请码
            $userCode= '';
            $is_unique = true;
            while($is_unique){
                $userCode = UserInfo::GenerateCode($userInfo->id);
                $isExistCode = UserInfo::getInfoByInviteCode($userCode);
                if(empty($isExistCode)){
                    $is_unique = false;
                }
            }
            $userInfo->invite_code = $userCode;
            if(!$userInfo->save()){
                throw new Exception('生成用户邀请码出错');
            }
            $message = new MessageList();
            $message->attributes = [
                'type_id'=>2,
                'title'=>'新用户消息',
                'content'=>'感谢您注册成为双天酒客户，这里好酒多多，开通会员更有专享活动，赶紧来看看吧!',
                'own_id'=>$userInfo->id,
                'target'=>1,
                'status'=>0,
                'publish_at'=>date('Y-m-d')
            ];
            if(!$message->save()){
                throw new Exception('生成用户消息出错');
            }
            $transaction->commit();
            Yii::$app->cache->delete('message_'.$phone);
            $data = [
                'phone'=>$phone,
                'password'=>$phone,
            ];
            return $this->showResult(200,'注册成功',$data);
        }catch (Exception $e){
            $transaction->rollBack();
            return $this->showResult(400,$e->getMessage());
        }
    }


    /**
     * @return array
     * 登录接口
     */
    public function actionLogin(){
        //获取表单数据
        $phone = Yii::$app->request->post('phone','');
        $password = Yii::$app->request->post('password','');
        $reg_id = Yii::$app->request->post('reg_id','');
        $reg_type = Yii::$app->request->post('reg_type',1);
        //判断是否获取到数据
        if(empty($phone)){
            return $this->showResult(301,'未获取到您的手机号');
        }
        if(empty($password)){
            return $this->showResult(301,'请输入你的登录密码');
        }
        //判断手机号格式
        if(!$this->validateMobilePhone($phone)){
            return $this->showResult(303,'手机格式错误');
        }
        //判断是否已注册
        $isExist = UserLogin::findIdentityByUsername($phone);
        if(empty($isExist)){
            return $this->showResult(303,'该用户尚未注册，请前往注册');
        }
        //判断密码
        if($isExist->password != md5(Yii::$app->params['pwd_pre'].$password)){
            return $this->showResult(303,'密码错误，请重新填写');
        }
        //判断用户信息是否存在
        $userInfo = $isExist->userInfo;
        if(empty($userInfo)){
            return $this->showResult(305,'未找到您的用户信息，请联系客服解决');
        }
        //生成新的token，并存到数据库
        $token = Yii::$app->security->generateRandomString();
        $isExist->attributes = [
            'token'=>$token,
            'last_login_time'=>date('Y-m-d H:i:s'),
            'reg_id'=>$reg_id,
            'reg_type'=>$reg_type,
        ];
        if(!$isExist->save()){
            return $this->showResult(400,'系统异常，登录失败');
        }
        $data = [
            'token'=>$token,
            'headUrl'=>empty($userInfo->head_url) ? Yii::$app->params['img_path'].'/logo/user_default.jpg':Yii::$app->params['img_path'].$userInfo->head_url,
            'phone'=>$phone,
            'nickName'=>$userInfo->nickname,
            'realNmae'=>$userInfo->realname,
            'sex'=>$userInfo->sex,
            'birthday'=>$userInfo->birth,
            'isVip'=>$userInfo->is_vip,
            'status'=>$userInfo->status,
        ];
        return $this->showResult(200,'登陆成功',$data);
    }

    /**
     * 上传头像接口
     */

    public function actionUploadHead(){
        $user_id = Yii::$app->user->identity->uid;
        //获取数据
        $post = Yii::$app->request->isPost;
        if(!$post){
            return $this->showResult(301,'未上传图片');
        }
        //解析二进制数据到文件中
        $file = UploadedFile::getInstanceByName('head');
        //生成文件名和路径
        $fileName = rand(1111111111,9999999999).$user_id.'.'.$file->extension;
        $filePath = '../../photo/logo/'.$fileName;
        //存放
        if($file->saveAs($filePath)){
            return $this->showResult(200,'上传成功','/logo/'.$fileName);
        }else{
            return $this->showResult(303,'系统异常，上传失败');
        }
    }

    /**
     * 修改资料接口
     */
    public function actionUpdate(){
        //获取表单参数
        $login = Yii::$app->user->identity;
        $key = Yii::$app->request->post('key','');//字段名
        $value = Yii::$app->request->post('value','');//字段值
        if(empty($key)||empty($value)){
            return $this->showResult(301,'未获取到请求数据');
        }
        $user = $login->userInfo;
        if(empty($user)){
            return $this->showResult(305,'未找到您的用户信息，请联系客服解决');
        }
        $user->attributes = [
            $key=>$value,
            'updated_time'=>date('Y-m-d H:i:s'),
        ];
        if(!$user->save()){
            return $this->showResult(400,'系统异常，保存失败');
        }else{
            return $this->showResult(200,'保存成功');
        }
    }

    /**
     * 忘记密码接口
     */
    public function actionResetPwd(){
        //获取表单参数
        $phone = Yii::$app->request->post('phone','');
        $code = Yii::$app->request->post('code','');
        $password = Yii::$app->request->post('password','');
        $confirmPwd = Yii::$app->request->post('confirmPwd','');
        //判断数据是否完整获取
        if(empty($phone)){
            return $this->showResult(301,'未获取到您的手机号');
        }
        if(empty($password)||empty($confirmPwd)){
            return $this->showResult(301,'密码选项不能为空');
        }
        if(empty($code)){
            return $this->showResult(301,'未获取到您的验证码');
        }
        if($password!==$confirmPwd){
            return $this->showResult(301,'两次输入的密码不一致');
        }
        //判断手机号格式
        if(!$this->validateMobilePhone($phone)){
            return $this->showResult(303,'手机格式错误');
        }
        //判断是否已注册
        $isExist = UserLogin::findIdentityByUsername($phone);
        if(empty($isExist)){
            return $this->showResult(303,'该手机号未注册，请先注册');
        }
        //判断验证码是否正确
        $codeCache = Yii::$app->cache->get('message_'.$phone);
        if($codeCache===false){
            return $this->showResult(303,'验证码已过期，请重新获取');
        }elseif($codeCache!=$code){
            return $this->showResult(303,'验证码错误，请重新输入');
        }
        //执行修改密码和存入数据库操作
        $isExist->password = md5(Yii::$app->params['pwd_pre'].$password);
        if(!$isExist->save()){
            return $this->showResult(400,'修改密码失败');
        }else{
            return $this->showResult(200,'修改密码成功');
        }
    }

    /**
     *我的消息列表接口
     */
    public function actionMessageList(){
        $user_id = Yii::$app->user->identity->uid;
//        $messageLists
    }
}