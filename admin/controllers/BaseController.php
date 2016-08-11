<?php

namespace admin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class BaseController extends Controller {

    public function renderJson($params = array()) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $params;
    }

    public function showResult($code=200,$message='',$data=[]){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'status'=>(string)$code,
            'message'=>$message,
        ];
        if(!empty($data)){
            $result['data'] = $data;
        }
        return $result;
    }

    public function validateMobilePhone($mobilephone){
        return preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9][0-9]{8}|17[0-9]{9}$|14[0-9]{9}$/",$mobilephone) && strlen($mobilephone)==11;
    }

}