<?php
namespace api\controllers;


use api\models\GoodInfo;
use api\models\GoodRush;
use api\models\GoodType;
use api\models\GoodVip;
use Yii;
use yii\helpers\ArrayHelper;

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
        $data = [];
        $types = GoodType::findAll(['is_active'=>1]);
        foreach($types as $type){
            $smell = $type->getTypes($type->goodSmells);
            $boot = $type->getTypes($type->goodBoots);
            $brand = $type->getTypes($type->goodBrands);
            $breed = $type->getTypes($type->goodBreeds);
            $color = $type->getTypes($type->goodColors);
            $country = $type->getTypes($type->goodCountries);
            $dry = $type->getTypes($type->goodDries);
            $model = $type->getTypes($type->goodModels);
            $priceField = ArrayHelper::getColumn($type->goodPriceFields,'discription');
            $style = $type->getTypes($type->goodStyles);
            $data[] = [
                'id'=>$type->id,
                'name'=>$type->name,
                'brand'=>$brand,
                'smell'=>$smell,
                'boot'=>$boot,
                'priceField'=>$priceField,
                'color'=>$color,
                'dry'=>$dry,
                'breed'=>$breed,
                'country'=>$country,
                'style'=>$style,
                'model'=>$model,
            ];
        }
        return $this->showResult(200,'成功',$data);
    }

    /**
     * @return array
     * 首页数据
     */
    public function actionHome(){
        //产品类型
        $type = GoodType::find()->select(['id','name','logo'])->where(['is_active'=>1])->asArray()->all();
        //抢购产品
        $rushList = GoodRush::find()->joinWith('g')->where("good_rush.is_active=1 and start_at<='".date('H:i:s')."' and end_at>='".date('H:i:s')."'")->one();
        $rush = [];
        if(!empty($rushList)){
            $rush = [
                'good_id'=>$rushList->gid,
                'pic'=>$rushList->g->pic,
                'end_at'=>$rushList->end_at,
                'name'=>$rushList->g->name,
                'volum'=>$rushList->g->volum,
                'rush_price'=>$rushList->price,
                'original_price'=>$rushList->g->price,
                'unit'=>$rushList->g->unit,
            ];
        }
        //会员产品
        $vipList = GoodVip::find()->joinWith('g')->where('good_vip.is_active=1')->one();
        $vip = [];
        if(!empty($vipList)){
            $vip = [
                'good_id'=>$vipList->gid,
                'pic'=>$vipList->g->pic,
                'name'=>$vipList->g->name,
                'number'=>$vipList->g->number,
                'limit'=>$vipList->limit,
                'volum'=>$vipList->g->volum,
                'vip_price'=>$vipList->price,
                'original_price'=>$vipList->g->price,
                'unit'=>$vipList->g->unit,
            ];
        }
        //热销产品
        $goods = GoodInfo::GoodList(1);
        $data = [
            'type'=>$type,
            'rush'=>$rush,
            'vip'=>$vip,
            'hot'=>$goods[0],
        ];
        return $this->showResult(200,'成功',$data);
    }


    /**
     * 抢购列表接口
     */
    public function actionRushList(){
        //获取页数,默认为1
        $page = Yii::$app->request->post('page',1);
        $pageSize = Yii::$app->params['pageSize'];
        //查询抢购
        $query = GoodRush::find()->joinWith('g');
        $query->where("good_rush.is_active=1 and start_at<='".date('H:i:s')."' and end_at>='".date('H:i:s')."'");
        $count = $query->count();
        $query->offset(($page-1)*$pageSize)->limit($pageSize);
        $goods = $query->all();
        $data = [];
        //处理获取到得数据
        if(!empty($goods)){
            $data = ArrayHelper::getColumn($goods,function($element){
                return [
                    'good_id'=>$element->gid,
                    'pic'=>$element->g->pic,
                    'end_at'=>$element->end_at,
                    'name'=>$element->g->name,
                    'volum'=>$element->g->volum,
                    'number'=>$element->g->number,
                    'rush_price'=>$element->price,
                    'original_price'=>$element->g->price,
                    'unit'=>$element->g->unit,
                ];
            });
        }
        return $this->showList(200,'成功',$count,$data);
    }

    /**
     * 会员列表接口
     */
    public function actionVipList(){
        //获取页数,默认为1
        $page = Yii::$app->request->post('page',1);
        $pageSize = Yii::$app->params['pageSize'];
        //查询抢购
        $query = GoodVip::find()->joinWith('g');
        $query->where('good_vip.is_active=1');
        $count = $query->count();
        $query->offset(($page-1)*$pageSize)->limit($pageSize);
        $goods = $query->all();
        $data = [];
        //处理获取到得数据
        if(!empty($goods)){
            $data = ArrayHelper::getColumn($goods,function($element){
                return [
                    'good_id'=>$element->gid,
                    'pic'=>$element->g->pic,
                    'name'=>$element->g->name,
                    'number'=>$element->g->number,
                    'limit'=>$element->limit,
                    'volum'=>$element->g->volum,
                    'vip_price'=>$element->price,
                    'original_price'=>$element->g->price,
                    'unit'=>$element->g->unit,
                ];
            });
        }
        return $this->showList(200,'成功',$count,$data);
    }

    /**
     * 热销列表接口
     */
    public function actionHotList(){
        //获取页数,默认为1
        $page = Yii::$app->request->post('page',1);
        //热销产品
        $result = GoodInfo::GoodList($page);
        return $this->showList(200,'成功',$result[1],$result[0]);
    }

    /**
     * 产品列表接口
     */
    public function actionList(){
        $from = Yii::$app->request->post('from',1);
    }

}