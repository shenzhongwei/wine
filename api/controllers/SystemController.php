<?php
namespace api\controllers;
/**
 * Created by PhpStorm.
 * User: 沈小鱼
 * Date: 2016/8/30
 * Time: 18:38
 */
    use admin\models\Zone;
    use common\helpers\ArrayHelper;
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


        /**
         * 城市列表接口
         * 去商户表中找去所有城市，按照首字母进行数组分割
         */
        public function actionCities(){
            $sql = "select * from shop_info WHERE merchant IN (SELECT merchant FROM good_info) AND city<>''";
            $cities = Yii::$app->db->createCommand($sql)->queryAll();
            if(empty($cities)){
                return $this->showResult(304,'暂无开通城市');
            }else{
                return $this->showResult(200,'成功',array_values(array_unique(array_column($cities,'city'))));
            }
        }
    }