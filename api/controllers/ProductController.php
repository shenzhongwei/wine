<?php
namespace api\controllers;

use admin\models\Zone;
use api\models\GoodBrand;
use api\models\GoodSmell;
use api\models\GoodType;
use api\models\PriceField;
use Yii;

/**
 * Created by PhpStorm.
 * User: me
 * Date: 2016/8/10
 * Time: 15:06
 */

class ProductController extends ApiController{

    /**
     * 搜索商品页面，分类所搜列表
     */
    public function actionSearchList(){
        $type = GoodType::find()->select(['id','name'])->where(['is_active'=>1])->asArray()->all();
        $brand = GoodBrand::find()->select(['id','name','logo'])->where(['is_active'=>1])->asArray()->all();
        $smell = GoodSmell::find()->select(['id','name'])->where(['is_active'=>1])->asArray()->all();
        $zone = Zone::find()->select(['id','name'])->where(['status'=>'1','leveltype'=>1])->asArray()->all();
        $price_field = PriceField::find()->select(['discription'])->asArray()->all();
        $data = [
            'type'=>$type,
            'brand'=>$brand,
            'smell'=>$smell,
            'zone'=>$zone,
            'price_field'=>array_column($price_field,'discription'),
        ];
        return $this->showResult(200,'成功',$data);
    }

    /**
     * @return array
     * 首页分类信息
     */
    public function actionType(){
        $type = GoodType::find()->select(['id','name','logo'])->where(['is_active'=>1])->asArray()->all();
        return $this->showResult(200,'成功',$type);
    }


    /**
     * 产品列表接口
     */
    public function actionList(){
        $from = Yii::$app->request->post('from',1);
    }

}