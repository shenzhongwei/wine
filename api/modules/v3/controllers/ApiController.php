<?php

namespace v3\controllers;
use Yii;
use yii\web\Controller;
use api\ext\auth\QueryParamAuth;
class ApiController extends Controller
{
	/**
	asdfsadfsdf阿斯顿法师打发
	*/
    public function behaviors(){
    	$behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'except'=>['ads','alipaypocket','wxpaypocket','alipayvat','wxpayvat','shops','boot','current','newact','wxpayticket','alipayticket','overtime-ticket-push','productlist'],
        ];
    	$behaviors['verbs'] = [
    			'class'=> \yii\filters\VerbFilter::className(),
				'actions'=>[
					'ads' =>['get'],
					'boot'=>['get'],
					'overtime-ticket-push'=>['get'],
					'*'=>['post']
				]
    		];
    	return $behaviors;
    }
    
    /**
     * 验证手机号格式方法
     * @param unknown $mobilephone
     * @return boolean
     */
    public function validateMobilePhone($mobilephone){
    	return preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9][0-9]{8}|17[0-9]{9}$|14[0-9]{9}$/",$mobilephone) && strlen($mobilephone)==11;
    }

    public function showResult($code=200,$message='',$data=[]){
        $result = [
            'status'=>(string)$code,
            'message'=>$message,
        ];
        if(!empty($data)){
            $result['data'] = $data;
        }
       

        return $result;
    }
    
    public function showList($code=200,$message='',$totalval,$data=[]){
    	$result = [
    			'status'=>(string)$code,
    			'message'=>$message,
    			'totalval' => (string)$totalval,
    	];
    	if(!empty($data)){
    		$result['data'] = $data;
    	}  
    	return $result;
    }
}