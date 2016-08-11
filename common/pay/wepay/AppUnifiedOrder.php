<?php
/**
 * Created by PhpStorm.
 * User: szw
 * Date: 2015/12/3
 * Time: 9:10
 */
/**
 * ͳ统一支付接口类
 */
namespace common\pay\wepay;

use common\pay\wepay\helpers\WxPayClientPub;
use common\pay\wepay\helpers\WxPayException;
use Yii;

class AppUnifiedOrder extends WxPayClientPub{
    function __construct()
    {
        //设置接口链接
        $this->url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //设置curl超时时间
        $this->curl_timeout = WxPayConfig::CURL_TIMEOUT;
    }

    /**
     * 生成接口参数xml
     */
    function createXml()
    {
        try
        {
            //检测必填参数
            if($this->parameters["out_trade_no"] == null)
            {
                throw new WxPayException("缺少统一支付接口必填参数out_trade_no！"."<br>");
            }elseif($this->parameters["body"] == null){
                throw new WxPayException("缺少统一支付接口必填参数body！"."<br>");
            }elseif ($this->parameters["total_fee"] == null ) {
                throw new WxPayException("缺少统一支付接口必填参数total_fee！"."<br>");
            }elseif ($this->parameters["notify_url"] == null) {
                throw new WxPayException("缺少统一支付接口必填参数notify_url！"."<br>");
            }elseif ($this->parameters["trade_type"] == null) {
                throw new WxPayException("缺少统一支付接口必填参数trade_type！"."<br>");
            }
            $this->parameters["appid"] = WxPayConfig::APPID;//公众账号ID
            $this->parameters["mch_id"] = WxPayConfig::MCHID;//商户号
            $this->parameters["spbill_create_ip"] = $_SERVER['REMOTE_ADDR'];//终端ip
//            $this->parameters["spbill_create_ip"] = '120.25.144.153';
            $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters);//签名
            return  $this->arrayToXml($this->parameters);
        }catch (WxPayException $e)
        {
            die($e->errorMessage());
        }
    }

    /**
     * 获取prepay_id
     */
    function getResult()
    {
        $this->postXml();
        $this->result = $this->xmlToArray($this->response);
        $data = $this->result;
        return $data;
    }
}