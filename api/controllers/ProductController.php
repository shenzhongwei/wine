<?php
namespace api\controllers;


use api\models\CommentDetail;
use api\models\GoodCollection;
use api\models\GoodInfo;
use api\models\GoodRush;
use api\models\GoodType;
use api\models\GoodVip;
use api\models\ShopInfo;
use api\models\UserLogin;
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
                'pic'=>Yii::$app->params['img_path'].$rushList->g->pic,
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
                'pic'=>Yii::$app->params['img_path'].$vipList->g->pic,
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
        $query = GoodInfo::find()->joinWith('goodRush');
        $query->where('good_info.is_active=1');
        $count = $query->count();
        $query->offset(($page-1)*$pageSize)->limit($pageSize);
        $goods = $query->all();
        $data = [];
        //处理获取到得数据
        if(!empty($goods)){
            $data = GoodInfo::data($goods);
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
        //查询会员
        $query = GoodInfo::find()->joinWith('goodVip');
        $query->where('good_info.is_active=1');
        $count = $query->count();
        $query->offset(($page-1)*$pageSize)->limit($pageSize);
        $goods = $query->all();
        $data = [];
        //处理获取到得数据
        if(!empty($goods)){
            $data = GoodInfo::data($goods);
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
    public function actionGoodList(){
        //列表入口来源 0大类 1商店
        $from = Yii::$app->request->post('from',0);
        //来源值
        $from_id = Yii::$app->request->post('from_id',1);
        //大类下的小分类，先获取key
        $key = Yii::$app->request->post('key','');
        //获取小分类的value
        $value = Yii::$app->request->post('value',0);
        //页数
        $page = Yii::$app->request->post('page',1);
        //排序字段 0 无 1价格 2 销量
        $sortKey = Yii::$app->request->post('sort_key','');
        //排序 0默认 1升序 2降序
        $sortValue = Yii::$app->request->post('sort_val',0);
        $pageSize = Yii::$app->params['pageSize'];
        $query = GoodInfo::find()->where(['is_active'=>1]);
        if($key=='price'){
            $value = explode('~',$value);
        }
        if($from == 0){//为0表示大类下列表
            $query->andWhere(['type'=>$from_id]);
            if(!empty($key)&&!empty($value)){
                $query->andWhere(['and',$key=='price' ? "$key between $value[0] and $value[1]":"$key=$value"]);
            }else{
                return $this->showResult(301,'获取数据异常');
            }
        }elseif($from == 1){//为1表示店铺上架下的列表
            $shop = ShopInfo::findOne($from_id);
            if(empty($shop)){
                return $this->showResult(303,'未获取到店铺信息');
            }
            $query->andWhere(['merchant'=>$shop->merchant]);
        }else{//其他值不识别
            return $this->showResult(301,'获取数据异常');
        }
        $count = $query->count();
        //排序
        if(empty($sortKey)||empty($sortValue)){
            //默认排序
            $query->orderBy(['order'=>SORT_ASC]);
        }elseif(!empty($sortKey)&&!empty($sortValue)){
            if($sortKey=='price'){//按价格
                $query->orderBy(['price'=>$sortValue==1 ? SORT_ASC:SORT_DESC,'order'=>SORT_ASC]);
            }elseif($sortKey=='sale'){//按销量
                $query->select(['good_info.*','sum(order_detail.amount) as sum'])->joinWith('orderDetails')->groupBy(['order_detail.gid']);
                $query->orderBy(['sum'=>$sortValue==1 ? SORT_ASC:SORT_DESC,'order'=>SORT_ASC]);
            }else{//其他排序不识别
                return $this->showResult(301,'获取数据异常');
            }
        }else{//其他来源不识别
            return $this->showResult(301,'获取数据异常');
        }
        //分页
        $query->offset(($page-1)*$pageSize)->limit($pageSize);
        $goods = $query->all();
        $data = [];
        //处理获取到得数据
        if(!empty($goods)){
            $data = GoodInfo::data($goods);
        }
        return $this->showList(200,'成功',$count,$data);
    }

    /**
     * 产品详情
     */
    public function actionGoodDetail(){
        //获取商品id
        $good_id = Yii::$app->request->post('good_id');
        if(empty($good_id)){
            return $this->showResult(301,'获取数据异常');
        }
        //判断产品信息是否存在
        $goodInfo = GoodInfo::findOne(['id'=>$good_id,'is_active'=>1]);
        if(empty($goodInfo)){
            return $this->showResult(303,'未获取到产品信息');
        }
        //产品轮播图;
        $pics = ArrayHelper::getColumn($goodInfo->goodPics,function($element){
            return Yii::$app->params['img_path'].$element->pic;
        });
        //查找评论
        $query = CommentDetail::find()->joinWith('c')->where(['gid'=>$good_id,'comment_detail.status'=>1])->orderBy(['order_comment.add_at'=>SORT_DESC]);
        $query->offset(0)->limit(2);
        $comments = $query->all();
        $comment = CommentDetail::data($comments);
        //处理详情
        $is_rush = empty($goodInfo->goodRush) ? 0:1;
        $is_vip = empty($goodInfo->goodVip) ? 0:1;
        if($is_rush == 1){
            $salePrice = $goodInfo->goodRush->price;
            $limit = $goodInfo->goodRush->limit;
        }elseif($is_vip == 1){
            $salePrice = $goodInfo->goodVip->price;
            $limit = $goodInfo->goodVip->limit;
        }else{
            $limit = 0;
            $salePrice = $goodInfo->price;
        }
        //根据用户是否登录判断是否可收藏
        $token = Yii::$app->request->post('token');
        if(empty($token)){
            $is_collected = 0;
        }else{
            $userLogin = UserLogin::findOne(['token'=>$token]);
            if(empty($userLogin)||empty($userLogin->uid)){
                $is_collected = 0;
            }else{
                //判断该商品是否已收藏
                $collectedGood = GoodCollection::find()->where(['uid'=>$userLogin->uid,'gid'=>$good_id,'status'=>1])->one();
                if(!empty($collectedGood)){
                    $is_collected = 1;
                }else{
                    $is_collected = 0;
                }
            }
        }
        $data = [
            'good_id'=>$goodInfo->id,
            'pics'=>$pics,
            'name'=>$goodInfo->name,
            'volum'=>$goodInfo->volum,
            'number'=>$goodInfo->number,
            'sale_price'=>$salePrice,
            'end_at' => $is_rush==1 ? $goodInfo->goodRush->end_at : '',
            'original_price'=>$goodInfo->price,
            'unit'=>$goodInfo->unit,
            'is_rush'=>$is_rush,
            'is_vip'=>$is_vip,
            'limit'=>$limit,
            'comments'=>$comment,
            'is_collected'=>$is_collected,
            'dateal'=>stripcslashes($goodInfo->detail),
        ];
        return $this->showResult(200,'详情如下',$data);
    }
}