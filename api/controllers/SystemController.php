<?php
namespace api\controllers;
/**
 * Created by PhpStorm.
 * User: 沈小鱼
 * Date: 2016/8/30
 * Time: 18:38
 */
    use Yii;
    class SystemController extends ApiController{

        /**
         * 设置页面关于我们和联系电话api
         */
        public function actionSetting(){
            $servicePhone = Yii::$app->params['servicePhone'];
            $aboutUs = Yii::$app->params['aboutUs'];
            $data = [
                'service_phone'=>$servicePhone,
                'about_us'=>$aboutUs,
            ];
            return $this->showResult(200,'成功',$data);
        }
        
    }