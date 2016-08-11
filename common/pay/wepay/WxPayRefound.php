<?php

namespace common\pay\wepay;

use common\pay\wepay\helpers\WxPayClientPub;
use common\pay\wepay\helpers\WxPayException;
use Yii;
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2016/5/25
 * Time: 15:01
 */
/**
 * 退款申请接口
 */
class WxPayRefound extends WxPayClientPub
{

    function __construct()
    {
        //设置接口链接
        $this->url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        //设置curl超时时间
        $this->curl_timeout = WxPayConfig::CURL_TIMEOUT;
    }

    /**
     * 生成接口参数xml
     */
    function createXml()
    {
        try {
            //检测必填参数
            if (empty($this->parameters["out_trade_no"]) && empty($this->parameters["transaction_id"])) {
                throw new WxPayException("退款申请接口中，out_trade_no、transaction_id至少填一个！" . "<br>");
            } elseif ($this->parameters["out_refund_no"] == null) {
                throw new WxPayException("退款申请接口中，缺少必填参数out_refund_no！" . "<br>");
            } elseif ($this->parameters["total_fee"] == null) {
                throw new WxPayException("退款申请接口中，缺少必填参数total_fee！" . "<br>");
            } elseif ($this->parameters["refund_fee"] == null) {
                throw new WxPayException("退款申请接口中，缺少必填参数refund_fee！" . "<br>");
            }
            $this->parameters["op_user_id"] = WxPayConfig::MCHID;//公众账号ID
            $this->parameters["appid"] = WxPayConfig::APPID;//公众账号ID
            $this->parameters["mch_id"] = WxPayConfig::MCHID;//商户号
            $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters);//签名
            return $this->arrayToXml($this->parameters);
        } catch (WxPayException $e) {
            die($e->errorMessage());
        }
    }

    /**
     * 	作用：获取结果，使用证书通信
     */
    function getResult()
    {
        $this->postXmlSSL();
        $this->result = $this->xmlToArray($this->response);
        return $this->result;
    }
}