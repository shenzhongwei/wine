<?php
namespace api\controllers;
/**
 * Created by PhpStorm.
 * User: szw
 * Date: 2016/8/29
 * Time: 15:46
 */
use api\models\AccountInout;
use api\models\InoutPay;
use api\models\MessageList;
use api\models\OrderInfo;
use api\models\OrderPay;
use api\models\PointInout;
use api\models\UserAccount;
use api\models\UserInfo;
use api\models\UserPoint;
use common\pay\alipay\AlipayNotify;
use common\pay\alipay\helpers\AlipayHelper;
use common\pay\wepay\AppUnifiedOrder;
use common\pay\wepay\helpers\Log;
use common\pay\wepay\helpers\WxPayServerPub;
use common\pay\wepay\WxPayConfig;
use daixianceng\smser\LuosimaoSmser;
use Yii;
use yii\base\Exception;

class PayController extends ApiController{

    /**
     * 微信统一下单接口
     */
    public function actionUnifiedOrder(){
        $user_id = Yii::$app->user->identity->getId();
        $type = Yii::$app->request->post('type');//类型 1订单 2充值
        $orderCode = Yii::$app->request->post('order_code');//对应的订单id
        //判断餐时是否传递
        if(empty($type)||empty($orderCode)){
            return $this->showResult(301,'读取订单信息出错');
        }
        $wxUnified = new AppUnifiedOrder();
        if($type==1){  //订单付款
            $orderInfo = OrderInfo::findOne(['order_code'=>$orderCode,'uid'=>$user_id,'state'=>1,'is_del'=>0]);
            if(empty($orderInfo)){
                return $this->showResult(304,'订单信息错误，请重试');
            }
            $wxUnified->setParameter('body','APP订单付款');
//            $wxUnified->setParameter('total_fee',1);
            $wxUnified->setParameter('total_fee',($orderInfo->pay_bill)*100);
            $wxUnified->setParameter('notify_url',WxPayConfig::NOTIFY_URL_ORDER);
        }elseif($type == 2){ //充值
            $inout_id = substr($orderCode,10);
            $inout = AccountInout::findOne(['target_id'=>$user_id,'id'=>$inout_id,'type'=>4,'status'=>2]);
            if(empty($inout)){
                return $this->showResult(304,'充值信息错误，请重试');
            }
            $wxUnified->setParameter('body','APP充值支付');
//            $wxUnified->setParameter('total_fee',1);
            $wxUnified->setParameter('total_fee',($inout->sum)*100);
            $wxUnified->setParameter('notify_url',WxPayConfig::NOTIFY_URL_BILL);
        }else{
            return $this->showResult(301,'数据异常');
        }

        $wxUnified->setParameter('spbill_create_ip',$_SERVER['REMOTE_ADDR']);
        $wxUnified->setParameter('out_trade_no',$orderCode);
        $wxUnified->setParameter('time_start',date('YmdHis',time()));
        $wxUnified->setParameter('time_expire',date('YmdHis',time()+900));
        $wxUnified->setParameter('trade_type','APP');
        $wxUnified->setParameter('goods_tag','SANTE');

        $res = $wxUnified->getResult();
        if($res['return_code']=='FAIL'){
            $data = $res['return_msg'];
            return $this->showResult(400,'下单失败',$data);
        }else{
            if($res['result_code'] == 'FAIL'){
                $data = $res['err_code_des'];
                return $this->showResult(400,'下单失败',$data);
            }else{
                $res['sign'] = $wxUnified->getSign($res);
                return $this->showResult(200,'下单成功',$res);
            }
        }
    }

    /**
     * 微信支付订单回调接口
     */
    public function actionWxPayOrder(){
        //使用通用通知接口
        $notify = new WxPayServerPub();
        $log = new Log();
        $log->log_result('开始微信订单回调');
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $log->log_result($xml);
        $callback = $notify->saveData($xml);
//        $log->log_result($callback);
        //以log文件形式记录回调信息
        $log->log_result('【接收到的notify通知】:'.$xml);
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        $result = $notify->checkSign();
        if($result){
            $log->log_result('验签成功');
            $params = [
                'order_code' => $callback['out_trade_no'],
                'pay_code' => $callback['transaction_id'],
                'pay_date' => $callback['time_end'],
                'pay_money' => $callback['total_fee']/100,
                'mch_id' => $callback['mch_id'],
                'pay_id' => 3,
            ];
            $result_status = $callback['result_code'];
            $return_status = $callback['return_code'];
            //返回正确执行操作
            if($result_status == 'SUCCESS' && $return_status == 'SUCCESS'){
                $res = OrderPay::Pay($params);
                if($res){
                    $log->log_result('保存数据成功');
                    echo 'SUCCESS';
                }else{
                    $log->log_result('保存数据失败');
                    echo 'FAIL';
                }
            }else{
                $log->log_result('返回状态错误');
                echo 'FAIL';
            }
        }else{
            $log->log_result('验签失败');
            echo 'FAIL';
        }
    }


    /**
     * 支付宝支付订单回调接口
     */
    public function actionAliPayOrder(){
        $verify = new AlipayNotify();
        $log = new AlipayHelper();
        $log->log_result('开始支付宝订单回调');
        $verify_result = $verify->verifyNotify();//验签
        if($verify_result){//成功
            $log->log_result('验签成功');
            //存入需要的参数
            $params = [
                'order_code' => $_POST['out_trade_no'],
                'pay_code' => $_POST['trade_no'],
                'pay_date' => $_POST['gmt_payment'],
                'pay_money' => $_POST['total_fee'],
                'mch_id' => $_POST['buyer_id'],
                'pay_id' => 2,
            ];
            $pay_status = $_POST['trade_status'];
            if($pay_status == 'TRADE_FINISHED' || $pay_status == 'TRADE_SUCCESS'){//返回状态正常
                $res = OrderPay::Pay($params);//调取类api，根据返回通知支付宝
                if($res){
                    $log->log_result('保存数据成功');
                    echo 'success';
                }else{
                    $log->log_result('保存数据失败');
                    echo 'fail';
                }
            }else{
                $log->log_result('返回状态错误');
                echo 'fail';
            }
        }else{
            $log->log_result('验签失败');
            echo 'fail';
        }
    }


    /**
     * 微信充值回调接口
     */
    public function actionWxPayAccount(){
        //使用通用通知接口
        $notify = new WxPayServerPub();
        $log = new Log();
        $log->log_result('开始微信充值回调');
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $log->log_result($xml);
        $callback = $notify->saveData($xml);
//        $log->log_result($callback);
        //以log文件形式记录回调信息
        $log->log_result('【接收到的notify通知】:'.$xml);
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        $result = $notify->checkSign();
        if($result){
            $log->log_result('验签成功');
            $params = [
                'order_code' => $callback['out_trade_no'],
                'pay_code' => $callback['transaction_id'],
                'pay_date' => $callback['time_end'],
                'pay_money' => $callback['total_fee']/100,
                'mch_id' => $callback['mch_id'],
                'pay_id' => 3,
            ];
            $result_status = $callback['result_code'];
            $return_status = $callback['return_code'];
            //返回正确执行操作
            if($result_status == 'SUCCESS' && $return_status == 'SUCCESS'){
                $res = InoutPay::Pay($params);
                if($res){
                    $log->log_result('保存数据成功');
                    echo 'SUCCESS';
                }else{
                    $log->log_result('保存数据失败');
                    echo 'FAIL';
                }
            }else{
                $log->log_result('返回状态错误');
                echo 'FAIL';
            }
        }else{
            $log->log_result('验签失败');
            echo 'FAIL';
        }
    }


    /**
     * 支付宝充值回调接口
     */
    public function actionAliPayAccount(){
        $verify = new AlipayNotify();
        $log = new AlipayHelper();
        $log->log_result('开始支付宝充值回调');
        $verify_result = $verify->verifyNotify();//验签
        if($verify_result){//成功
            $log->log_result('验签成功');
            //存入需要的参数
            $params = [
                'order_code' => $_POST['out_trade_no'],
                'pay_code' => $_POST['trade_no'],
                'pay_date' => $_POST['gmt_payment'],
                'pay_money' => $_POST['total_fee'],
                'mch_id' => $_POST['buyer_id'],
                'pay_id' => 2,
            ];
            $pay_status = $_POST['trade_status'];
            if($pay_status == 'TRADE_FINISHED' || $pay_status == 'TRADE_SUCCESS'){//返回状态正常
                $res = InoutPay::Pay($params);//调取类api，根据返回通知支付宝
                if($res){
                    $log->log_result('保存数据成功');
                    echo 'success';
                }else{
                    $log->log_result('保存数据失败');
                    echo 'fail';
                }
            }else{
                $log->log_result('返回状态错误');
                echo 'fail';
            }
        }else{
            $log->log_result('验签失败');
            echo 'fail';
        }
    }


    /**
     * 余额付款接口
     */
    public function actionAccountPayOrder(){
        $user_id = Yii::$app->user->identity->getId();
        $userInfo = UserInfo::findOne($user_id);
        if(empty($userInfo)){
            return $this->showResult(302,'用户信息异常');
        }
        $orderCode = Yii::$app->request->post('order_code');//对应的订单code
        $pay_password = Yii::$app->request->post('pay_password');//对应的订单code
        if(empty($orderCode)||empty($pay_password)){//未获取到返回错误
            return $this->showResult(301,'读取订单信息出错');
        }
        $orderInfo = OrderInfo::findOne(['uid'=>$user_id,'order_code'=>$orderCode,'state'=>1,'is_del'=>0]);//查找订单，炸不到返回错误
        if(empty($orderInfo)){
            return $this->showResult(304,'订单信息错误，请重试');
        }
        //查找账户余额，不足则提示
        $account = UserAccount::findOne(['target'=>$user_id,'type'=>1,'level'=>2,'is_active'=>1]);
        if(empty($account)||$account->end<$orderInfo->pay_bill){
            return $this->showResult(303,'账户余额不足，请先充值');
        }
        if(empty($account->pay_password)){
            return $this->showResult(305,'未设置余额付款密码，请前往设置');
        }

        if($account->pay_password != md5(Yii::$app->params['pay_pre'].$pay_password)){
            return $this->showResult(305,'余额付款密码错误，请重试');
        }
        /**
         * 开启事务
         * 1、修改订单状态
         * 2、更改账户余额
         * 3、记录账户明细
         * 4、用户余额变动消息提醒
         */
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $orderInfo->state=2;
            $orderInfo->pay_date = time();
            $orderInfo->pay_id = 1;
            if(!$orderInfo->save()){
                throw new Exception('更新订单状态出错',400);
            }
            $account->start = $account->end;
            $account->end = $account->end-$orderInfo->pay_bill;
            if(!$account->save()){
                throw new Exception('更改账户余额信息出错',400);
            }
            $userInout = new AccountInout();
            $userInout->attributes = [
                'aid'=>$account->id,
                'aio_date'=>time(),
                'type'=>1,
                'target_id'=>$orderInfo->id,
                'sum'=>$orderInfo->pay_bill,
                'discount'=>0,
                'status'=>1,
            ];
            if(!$userInout->save()){
                throw new Exception('记录账户明细出错',400);
            }
            $message = new MessageList();
            $message->attributes = [
                'type_id'=>2,
                'title'=>'余额支付成功',
                'content'=>'余额付款'.$orderInfo->pay_bill.'元；用途：订单支付;订单号：'.$orderInfo->order_code,'积分抵押：'.$orderInfo->point,
                'own_id'=>$user_id,
                'target'=>4,
                'status'=>0,
                'publish_at'=>date('Y-m-d')
            ];
            if(!$message->save()){
                throw new Exception('生成用户余额变更消息出错');
            }
            if($orderInfo->point>0){
                $userPoint = UserPoint::findOne(['uid'=>$user_id,'is_active'=>1]);
                if(empty($userPoint)){
                    throw new Exception('用户积分账户异常',304);
                }
                $userPoint->point = $userPoint->point-$orderInfo->point;
                $userPoint->update_at = time();
                if(!$userPoint->save()){
                    throw new Exception('保存用户积分出错',400);
                }
                $pointInout = new PointInout();
                $pointInout->attributes = [
                    'uid'=>$orderInfo->uid,
                    'pid'=>$userPoint->id,
                    'pio_date'=>time(),
                    'pio_type'=>2,
                    'amount'=>$orderInfo->point,
                    'oid'=>$orderInfo->id,
                    'note'=>"用户$userInfo->nickname"."于".date('Y年m月d日 H时i分s秒')."，支出$orderInfo->point"."积分用于支付编号为".$orderCode."的订单",
                    'status'=>1,
                ];
                if(!$pointInout->save()){
                    throw new Exception('用户积分收入记录保存出错',400);
                }
            }
            $transaction->commit();
            $shop=$orderInfo->s;
            $phone = $shop->phone;
            if(self::validateMobilePhone($phone)) {
                //给店铺发短信
                $smser = new LuosimaoSmser();
//                $smser->username = Yii::$app->params['smsParams']['username'];
//                $smser->setPassword(Yii::$app->params['smsParams']['password']);
                $smser->setPassword(Yii::$app->params['smsParams']['api_key']);
                $content = "您有新的订单待处理，订单编号：$orderInfo->order_code，请尽快处理！【双天酒易购】";
                $res = $smser->send($phone, $content);
            }
            return $this->showResult(200,'余额付款成功');
        }catch (Exception $e){
            $transaction->rollBack();
            return $this->showResult(400,$e->getMessage());
        }
    }
}
