<?php
namespace api\controllers;
/**
 * Created by PhpStorm.
 * User: 11633
 * Date: 2016-11-10
 * Time: 13:22
 */

use api\models\PointInout;
use api\models\UserPoint;
use common\helpers\ArrayHelper;
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

    /**
     * 积分进出明细
     */
    public function actionPointList(){
        $user_id = Yii::$app->user->identity->getId();//判断用户
        //查出一个月所有的积分进出
        $userPoint = UserPoint::find()->where("uid=$user_id and is_active=1")->one();
        $data = [];
        if(!empty($userPoint)){
            $pointLists = PointInout::find()->where("pid=$userPoint->id and status=1 and pio_date>=".(time()-2592000))->all();
            if(!empty($pointLists)){
                $data = ArrayHelper::getColumn($pointLists,function($element){
                    $number = (int)$element->amount;
                    $amount = $number==$element->amount ? $number:$element->amount;
                    return [
                        'date'=>date('Y-m-d H:i',$element->pio_date),
                        'amount'=>$element->pio_type==1 ? '+'.$amount:'-'.$amount,
                    ];
                });
            }
        }
        return $this->showResult(200,'明细如下',$data);
    }
}