<?php
/**
 * Created by PhpStorm.
 * User: szw
 * Date: 2015/12/2
 * Time: 14:26
 */
namespace common\pay\wepay\helpers;

use Yii;
use \Exception;

class WxPayException extends Exception{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}