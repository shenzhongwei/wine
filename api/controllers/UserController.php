<?php
namespace api\controllers;

use api\models\MessageList;
use api\models\PromotionInfo;
use api\models\UserAccount;
use api\models\UserInfo;
use api\models\UserLogin;
use api\models\UserTicket;
use common\helpers\ArrayHelper;
use daixianceng\smser\Wxtsms;
use Yii;
use \Exception;
use yii\web\UploadedFile;

/**
 * Created by PhpStorm.
 * User: szw
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
        $smser = new Wxtsms();
        $smser->username = Yii::$app->params['smsParams']['username'];
        $smser->setPassword(Yii::$app->params['smsParams']['password']);
        $code = rand(111111,999999);
        $content = "【酒双天】您本次操作验证码为：".$code."，请在半小时内完成本次操作。如非本人操作，请忽略。";
        $res = $smser->sendSms($phone,$content);
        if($res){
            Yii::$app->cache->set('message_'.$phone,$code);
            return $this->showResult(200,'发送成功');
        }else{
            return $this->showResult(304,'发送失败',$smser->getMessage());
        }
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
            if(!empty($invitedUser)){
                //存入消息
                $Invitemessage = new MessageList();
                $Invitemessage->attributes = [
                    'type_id'=>2,
                    'title'=>'推荐成功',
                    'content'=>"手机号为$phone 的用户成功使用您的邀请码注册",
                    'own_id'=>$invitedUser->id,
                    'target'=>13,
                    'status'=>0,
                    'publish_at'=>date('Y-m-d')
                ];
                if(!$Invitemessage->save()){
                    throw new Exception('生成邀请消息出错');
                }
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
            //判断是否有新用户活动
            $promotions = PromotionInfo::find()->where(['and','pt_id=1','is_active=1','start_at<='.time(),'end_at>='.time(),'time>0'])->all();
            if(!empty($promotions)){
                $data = [];
                foreach($promotions as $promotion){
                    $start = empty($promotion->valid_circle) ? 0:time();
                    $end = empty($promotion->valid_circle) ? 0:time()+($promotion->valid_circle*86400);
                    for($i=0;$i<$promotion->time;$i++){
                        $data [] = [
                            $userInfo->id,$promotion->id,$start,$end,1
                        ];
                    }
                }
                $row = Yii::$app->db->createCommand()->batchInsert(UserTicket::tableName(),['uid','pid','start_at','end_at','status'],$data)->execute();
                if(empty($row)){
                    throw new Exception('保存新用户优惠券出错');
                }
            }
            //存入消息
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
                'password'=>$password,
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
            return $this->showResult(302,'未找到您的用户信息，请联系客服解决');
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
            Yii::$app->cache->delete('message_'.$phone);
            return $this->showResult(200,'修改密码成功');
        }
    }



    /**
     * 我的推荐人列表
     */
    public function actionRecommendList(){
        $user_id = Yii::$app->user->identity->getId();
        $page = Yii::$app->request->post('page',1);//页数
        $pageSize = Yii::$app->params['pageSize'];
        $query = UserInfo::find()->where(['status'=>1,'invite_user_id'=>$user_id]);//查
        $query->orderBy(['created_time'=>SORT_DESC]);//排序
        $count = $query->count();
        $query->offset(($page-1)*$pageSize)->limit($pageSize);//分页
        $recommentUsers = $query->all();
        $data = ArrayHelper::getColumn($recommentUsers,function($element){
            return [
                'user_id'=>$element->id,
                'nickName'=>$element->nickname,
                'phone'=>$element->phone,
                'headUrl'=>empty($element->head_url) ? Yii::$app->params['img_path'].'/logo/user_default.jpg':Yii::$app->params['img_path'].$element->head_url,
            ];
        });
        return $this->showList(200,'列表如下',$count,$data);
    }

    /**
     * 我的账户余额以及是否设置支付密码api
     */
    public function actionAccount(){
        $user_id = Yii::$app->user->identity->getId();
        //查找账户
        $account = UserAccount::findOne(['target'=>$user_id,'type'=>1,'level'=>2,'is_active'=>1]);
        //没有返回0
        if(empty($account)){
            $lastbill = 0;
            $set_pwd = 0;
        }else{
            //有返回剩余金额
            $lastbill = $account->end>0 ? $account->end:0;
            $set_pwd = empty($account->pay_password) ? 0:1;
        }
        $data = [
            'last_bill'=>$lastbill,
            'set_pwd'=>$set_pwd,
        ];
        return $this->showResult(200,'成功',$data);
    }

    /**
     * 设置余额支付密码
     */
    public function actionSetPayPwd(){
        $user_id = Yii::$app->user->identity->getId();
        $userInfo = UserInfo::findOne($user_id);//判断用户是否存在
        if(empty($userInfo)){
            return $this->showResult(302,'用户信息异常');
        }
        //接收数据
        $phone = Yii::$app->request->post('phone');
        $code = Yii::$app->request->post('code','');//验证码
        $password = (int)Yii::$app->request->post('password','');//密码
        $confirmPwd = Yii::$app->request->post('confirmPwd','');//确认密码
        if(empty($code)||empty($password)||empty($confirmPwd)||empty($phone)){//判断是否接受完整
            return $this->showResult(301,'获取数据出错');
        }
        if($phone!=$userInfo->phone){
            return $this->showResult(303,'请使用您登录的手机号进行验证');
        }
        if(strlen($password)!=6){
            return $this->showResult(301,'请输入6位数字密码');
        }
        if($confirmPwd!=$password){//判断两次输入密码是否一致
            return $this->showResult(301,'两次输入的密码不一致');
        }
        $codeCache = Yii::$app->cache->get('message_'.$userInfo->phone);//找到缓存中的密码
        if($codeCache===false){
            return $this->showResult(303,'验证码已过期，请重新获取');//验证code
        }elseif($codeCache!=$code){
            return $this->showResult(303,'验证码错误，请重新输入');
        }
        $userAccount = UserAccount::findOne(['target'=>$user_id,'type'=>1,'level'=>2]);//涨到用户余额
        if(!empty($userAccount->pay_password)){
            return $this->showResult(303,'该用户已设置余额支付密码，请勿重复设置');
        }
        if(empty($userAccount)){
            $userAccount = new UserAccount();
            $userAccount->create_at = time();
            $userAccount->start = 0;
            $userAccount->end = 0;
        }
        $userAccount->target=$user_id;
        $userAccount->level=2;
        $userAccount->type=1;
        $userAccount->is_active = 1;
        $userAccount->pay_password = md5(Yii::$app->params['pay_pre'].$password);
        $userAccount->update_at = time();
        if($userAccount->save()){
            Yii::$app->cache->delete('message_'.$userInfo->phone);
            return $this->showResult(200,'设置成功');
        }else{
            return $this->showResult(400,'设置失败，请重试');
        }
    }


    /**
     * 修改余额支付密码
     */
    public function actionModifyPayPwd(){
        $user_id = Yii::$app->user->identity->getId();
        $userInfo = UserInfo::findOne($user_id);//判断用户是否存在
        if(empty($userInfo)){
            return $this->showResult(302,'用户信息异常');
        }
        //接收数据
        $phone = Yii::$app->request->post('phone');
        $code = Yii::$app->request->post('code','');//验证码
        $password = (int)Yii::$app->request->post('password','');//密码
        $confirmPwd = Yii::$app->request->post('confirmPwd','');//确认密码
        if(empty($code)||empty($password)||empty($confirmPwd)||empty($phone)){//判断是否接受完整
            return $this->showResult(301,'获取数据出错');
        }
        if($phone!=$userInfo->phone){
            return $this->showResult(303,'请使用您登录的手机号进行验证');
        }
        if(strlen($password)!=6){
            return $this->showResult(301,'请输入6位数字密码');
        }
        if($confirmPwd!=$password){//判断两次输入密码是否一致
            return $this->showResult(301,'两次输入的密码不一致');
        }
        $codeCache = Yii::$app->cache->get('message_'.$userInfo->phone);//找到缓存中的密码
        if($codeCache===false){
            return $this->showResult(303,'验证码已过期，请重新获取');//验证code
        }elseif($codeCache!=$code){
            return $this->showResult(303,'验证码错误，请重新输入');
        }
        $userAccount = UserAccount::findOne(['target'=>$user_id,'type'=>1,'level'=>2]);//涨到用户余额
        if(empty($userAccount)||empty($userAccount->pay_password)){
            return $this->showResult(303,'该用户尚未设置余额支付密码，请前往设置');
        }
        $userAccount->is_active = 1;
        $userAccount->pay_password = md5(Yii::$app->params['pay_pre'].$password);
        $userAccount->update_at = time();
        if($userAccount->save()){
            Yii::$app->cache->delete('message_'.$userInfo->phone);
            return $this->showResult(200,'修改成功');
        }else{
            return $this->showResult(400,'修改失败，请重试');
        }
    }
}