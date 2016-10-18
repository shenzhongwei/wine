<?php
namespace admin\controllers;

use admin\models\UserLogin;
use Yii;
/**
 * Created by PhpStorm.
 * User: 11633
 * Date: 2016/10/18
 * Time: 15:00
 */
class ShareController extends BaseController {

    public function actionRecommend(){
        $token = Yii::$app->request->get('token');
        if(empty($token)||empty(UserLogin::findOne(['token'=>$token]))){
            $message = '网页已过期';
            $username = '';
            $recommendCode = '';
        }else{
            $user = UserLogin::findOne(['token'=>$token])->u;
            $message = '';
            $username = $user->nickname;
            $recommendCode = $user->invite_code;
        }
        return $this->renderAjax('recommend',[
            'message'=>$message,
            'name'=>$username,
            'code'=>$recommendCode,
        ]);
    }
}

?>