<?php
namespace api\controllers;
/**
 * Created by PhpStorm.
 * User: szw
 * Date: 2016/8/29
 * Time: 15:46
 */
use api\models\AccountInout;
use api\models\OrderInfo;
use api\models\OrderPay;
use common\pay\wepay\AppUnifiedOrder;
use common\pay\wepay\helpers\Log;
use common\pay\wepay\helpers\WxPayServerPub;
use common\pay\wepay\WxPayConfig;
use Yii;

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
        if($type==1){
            $orderInfo = OrderInfo::findOne(['order_code'=>$orderCode,'uid'=>$user_id,'state'=>1,'is_del'=>0]);
            if(empty($orderInfo)){
                return $this->showResult(303,'订单信息错误，请重试');
            }
            $wxUnified->setParameter('body','APP订单付款');
//            $wxUnified->setParameter('total_fee',1);
            $wxUnified->setParameter('total_fee',($orderInfo->pay_bill)*100);
            $wxUnified->setParameter('notify_url',WxPayConfig::NOTIFY_URL_ORDER);
        }elseif($type == 2){
            $inout_id = substr($orderCode,10);
            $inout = AccountInout::findOne(['target_id'=>$user_id,'id'=>$inout_id,'type'=>4,'status'=>2]);
            if(empty($inout)){
                return $this->showResult(303,'充值信息错误，请重试');
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
        $log->log_result('开始回调');
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
                'pay_money' => $callback['total_fee'],
                'mch_id' => $callback['mch_id'],
                'pay_id' => 3,
            ];
            $result_status = $callback['result_code'];
            $return_status = $callback['return_code'];
            if($result_status == 'SUCCESS' && $return_status == 'SUCCESS'){
                $res = OrderPay::Pay($params);
                if($res){
                    $log->log_result('保存数据成功');
                    echo 'SUCCESS';
                }else{
                    $log->log_result('保存数据失败');
                    echo 'FAIL';
                }
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

    }


    /**
     * 微信充值回调接口
     */
    public function actionWxPayAccount(){

    }


    /**
     * 支付宝充值回调接口
     */
    public function actionAliPayAccount(){

    }
}
