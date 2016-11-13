<?php
namespace common\map;

use yii\base\Object;
use Yii;

/**
 * Created by PhpStorm.
 * User: 11633
 * Date: 2016-11-12
 * Time: 15:50
 */

class Map extends Object {

    public $params;//请求参数，类型为关联数组
    public $result;//返回参数，类型为关联数组
    public $key = 'b52eaffeacc77c48af813f8f060eaae6';
    public $ip_url='http://restapi.amap.com/v3/ip?';//接口链接
    public $search_url = ' http://restapi.amap.com/v3/assistant/inputtips?';
    public $regeo_url = ' http://restapi.amap.com/v3/geocode/regeo?';

    public function IP(){
        $str = "key=$this->key&output=JSON";
        $url = $this->ip_url.$str;
        $result = json_decode($this->SendRequest($url),true);
        return $result;
    }

    public function InputTips($key,$location){
        $str = "key=$this->key&output=JSON&keywords=$key";
        if(!empty($location)){
            $str.="&location=$location";
        }
        $url = $this->search_url.$str;
        $result = json_decode($this->SendRequest($url),true);
        return $result;
    }

    public function Regeo($location){
        $str = "key=$this->key&output=JSON&extensions=base&location=$location";
        $url = $this->search_url.$str;
        $result = json_decode($this->SendRequest($url),true);
        return $result;
    }

    public function SendRequest($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}
?>