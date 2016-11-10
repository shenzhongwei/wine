<?php
namespace api\controllers;
/**
 * Created by PhpStorm.
 * User: 11633
 * Date: 2016-11-10
 * Time: 13:22
 */

use api\models\UserPoint;
use Yii;

class PointController extends ApiController {


    /**
     * @return array
     * 查询用户剩余积分接口
     */
    public function actionPoint(){
        $user_id = Yii::$app->user->identity->getId();//传递token匹配用户
        $userPoint = UserPoint::findOne(['uid'=>$user_id,'is_active'=>1]);
        if(empty($userPoint)){
            $point = 0;
        }else{
            $point = $userPoint->point;
        }
        $data = [
            'point'=>$point,
        ];
        return $this->showResult(200,'成功',$data);
    }

    public function actionPointList(){

    }
}