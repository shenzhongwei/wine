<?php
/**
 * Created by PhpStorm.
 * User: szw
 * Date: 2015/12/3
 * Time: 11:18
 */
namespace common\pay\wepay\helpers;

use Yii;
/**
 * 响应型接口基类
 */
class WxPayServerPub extends WxPayCommonPub
{
    public $data;//接收到的数据，类型为关联数组
    public  $returnParameters;//返回参数，类型为关联数组

    /**
     * 将微信的请求xml转换成关联数组，以方便数据处理
     */
    function saveData($xml)
    {
        $this->data = $this->xmlToArray($xml);
        return $this->data;
    }

    function checkSign()
    {
        $tmpData = $this->data;
        unset($tmpData['sign']);
        $sign = $this->getSign($tmpData);//本地签名
        if ($this->data['sign'] == $sign) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 获取微信的请求数据
     */
    function getData()
    {
        return $this->data;
    }

    /**
     * 设置返回微信的xml数据
     */
    function setReturnParameter($parameter, $parameterValue)
    {
        $this->returnParameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }

    /**
     * 生成接口参数xml
     */
    function createXml()
    {
        return $this->arrayToXml($this->returnParameters);
    }

    /**
     * 将xml数据返回微信
     */
    function returnXml()
    {
        $returnXml = $this->createXml();
        return $returnXml;
    }
}