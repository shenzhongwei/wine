<?php
namespace api\controllers;

use api\models\ShopInfo;
use api\models\UserAddress;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: me
 * Date: 2016/8/23
 * Time: 10:43
 */

class ShopController extends ApiController{

    /**
     * 商家分布
     */
    public function actionShopSpread(){
        //获取经纬度
        $lat = Yii::$app->request->post('lat');
        $lng = Yii::$app->request->post('lng');
        //若果没有获取到，报错
        if(empty($lng)||empty($lat)){
            return $this->showResult(301,'未获取到您的地址信息');
        }
        //搜索数据库找出5KM之内的店铺
        $query = ShopInfo::find()->select(['*']);
        $query->addSelect(["ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-lat/1000000*PI()/180)/2),2)+COS($lat*PI()/180)*COS(lat/1000000*PI()/180)*POW(SIN(($lng*PI()/180-lng/1000000*PI()/180)/2),2)))*1000) as distance"]);
        $query->where("is_active=1 and lat>0 and lng>0")->having('distance<=5000');
        $query->orderBy(['distance'=>SORT_ASC]);
        $shops = $query->all();
        $data = [];
        if(!empty($shops)){
            $data = ArrayHelper::getColumn($shops,function($element){
                return [
                    'shop_id'=>$element->id,
                    'name'=>$element->name,
                    'address'=>$element->province.$element->city.$element->district.$element->region.$element->address,
                    'lat'=>$element->lat/1000000,
                    'lng'=>$element->lng/1000000,
                ];
            });
        }
        return $this->showResult(200,'成功',$data);
    }


    /**
     * 确认订单页面商家列表
     */
    public function actionShopList(){
        $user_id = Yii::$app->user->identity->getId();
        $page = Yii::$app->request->post('page');
        $pageSize = Yii::$app->params['pageSize'];
        //获取地址id
        $address_id = Yii::$app->request->post('address_id');
        if(empty($address_id)){
            return $this->showResult(301,'未获取到您的地址信息');
        }
        //判断地址是否是该用户的,如果不是怎返回错误信息
        $userAddress = UserAddress::find()->where("lat>0 and lng>0 and id=$address_id and uid=$user_id and status=1")->one();
        if(empty($userAddress)){
            return $this->showResult(303,'未获取到您的地址信息');
        }
        //如果是的，则查出所有的商家
        $lat = $userAddress->lat/1000000;
        $lng = $userAddress->lng/1000000;
        //搜索数据库找出5KM之内的店铺
        $query = ShopInfo::find()->select(['*']);
        $query->addSelect(["ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-lat/1000000*PI()/180)/2),2)+COS($lat*PI()/180)*COS(lat/1000000*PI()/180)*POW(SIN(($lng*PI()/180-lng/1000000*PI()/180)/2),2)))*1000) as distance"]);
        $query->where("is_active=1 and lat>0 and lng>0");
        $count = $query->count();
        $query->orderBy(['distance'=>SORT_ASC]);
        $query->offset(($page-1)*$pageSize)->limit($pageSize);
        $shops = $query->asArray()->all();
        $data = [];
        if(!empty($shops)){
            $data = ArrayHelper::getColumn($shops,function($element){
                return [
                    'shop_id'=>$element['id'],
                    'name'=>$element['name'],
                    'distance'=>$element['distance'],
                    'lat'=>$element['lat']/1000000,
                    'lng'=>$element['lng']/1000000,
                    'tips'=>$element['distance']<=3000 ? '19分钟内送达':($element['distance']<5000 ? '29分钟内送达':'您的配送距离较长，请耐心等待'),
                ];
            });
        }
        return $this->showList(200,'成功',$count,$data);
    }
}