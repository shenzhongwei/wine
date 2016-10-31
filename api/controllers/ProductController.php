<?php
namespace api\controllers;


use api\models\CommentDetail;
use api\models\GoodCollection;
use api\models\GoodInfo;
use api\models\GoodRush;
use api\models\GoodType;
use api\models\GoodVip;
use api\models\HotSearch;
use api\models\OrderDetail;
use api\models\ShopInfo;
use api\models\UserLogin;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: szw
 * Date: 2016/8/10
 * Time: 15:06
 */

class ProductController extends ApiController{

    /**
     * 搜索商品页面，分类所搜列表
     */
    public function actionSearchList(){
        $data = [];
        $types = GoodType::find()->joinWith('goodInfos')->where("good_type.is_active=1 and good_type.name<>'' and good_info.id>0")->all();
        foreach($types as $type){
            /**
             * @var GoodType $type
             */
            if(empty($type->goodSmells)&&empty($type->goodBoots)&&empty($type->goodBrands)&&empty($type->goodBreeds)&&empty($type->goodColors)&&empty($type->goodCountries)&&
                empty($type->goodDries)&&empty($type->goodModels)&&empty($type->goodPriceFields)&&empty($type->goodStyles)){
                continue;
            }
            $smell = $type->getTypes($type->goodSmells);
            $boot = $type->getTypes($type->goodBoots);
            $brand = $type->getBrands($type->goodBrands);
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
//                'logo'=>Yii::$app->params['img_path'].$type->logo,
                'brand'=>$brand,
                'smell'=>$smell,
                'boot'=>$boot,
                'price'=>$priceField,
                'color'=>$color,
                'dry'=>$dry,
                'breed'=>$breed,
                'country'=>$country,
                'style'=>$style,
                'volum'=>$model,
            ];
        }
        return $this->showResult(200,'成功',$data);
    }



    /**
     * @return array
     * 首页数据
     */
    public function actionHome(){
        $token = Yii::$app->request->post('token');
        //普通商品类型1 会员商品类型2 抢购商品类型3
        //产品类型
        $types = GoodType::find()->joinWith('goodInfos')->addSelect(['good_type.id as id','good_type.name as name',"CONCAT('".Yii::$app->params['img_path']."',logo) as logo"])->where(['good_type.is_active'=>1])->all();
        $type = ArrayHelper::getColumn($types,function($element){
            return [
                'id'=>$element->id,
                'name'=>$element->name,
                'logo'=>$element->logo,
            ];
        });
        //抢购产品  抢购商品和商品信息关联
        $rushList = GoodRush::find()->joinWith('g')->leftJoin('merchant_info','good_info.merchant=merchant_info.id')
            ->leftJoin('good_type','good_info.type=good_type.id')->where(
            "good_rush.is_active=1 and start_at<=".time()." 
            and end_at>=".time()." and merchant_info.id>0 and good_info.is_active=1 
            and merchant_info.is_active=1 and good_info.merchant>0 and good_info.id>0 
            and good_type.id>0 and good_type.is_active=1"
        )->one();
        $rush = [];
        if(!empty($rushList)){
            if(!empty($token)){
                $userLogin = UserLogin::findOne(['token'=>$token]);
                if(empty($userLogin)){
                    $rest = $rushList->limit;
                }else{
                    $userInfo = $userLogin->userInfo;
                    if(empty($userInfo)){
                        return $this->showResult(302,'用户信息状态异常');
                    }
                    $order = OrderDetail::find()->joinWith('o')->addSelect(["SUM(amount) as sum"])
                        ->where("type=3 and state between 2 and 7 and uid=$userLogin->uid and gid=".$rushList->gid." and 
                        rush_id=$rushList->id and order_date>=$rushList->start_at and order_date<=$rushList->end_at")->one();
                    $buyNum =$order->sum;
                    $rest = $rushList->limit-$buyNum;
                }
            }else{
                $rest = $rushList->limit;
            }
            $payArr = explode('|',$rushList->rush_pay);
            $rush = [
                'good_id'=>$rushList->gid,
                'pic'=>Yii::$app->params['img_path'].$rushList->g->pic,
                'name'=>$rushList->g->name,
                'volum'=>$rushList->g->volum, //毫升
                'number'=>$rushList->g->number,
                'amount'=>$rushList->amount<0 ? 0:$rushList->amount,
                'limit'=>$rushList->limit,
                'rest'=>$rest<0 ? 0:$rest,
                'end_at'=>date('Y-m-d H:i:s',$rushList->end_at),
                'sale_price'=>$rushList->price,
                'original_price'=>$rushList->g->price,
                'unit'=>$rushList->g->unit,
                'cash_pay'=>in_array('1',$payArr) ? 1:0,
                'ali_pay'=>in_array('2',$payArr) ? 1:0,
                'we_pay'=>in_array('3',$payArr) ? 1:0,
                'point_sup'=>$rushList->point_sup,
                'type'=>3,
                'operate'=>1,
            ];
        }
        //会员产品
        $vipList = GoodInfo::find()->joinWith(['merchant0','type0'])
            ->where('good_info.is_active=1 and good_info.merchant>0 and good_info.vip_show=1 and 
            merchant_info.id>0 and merchant_info.is_active=1 and 
            good_type.id>0 and good_type.is_active=1')->orderBy(['good_info.order'=>SORT_ASC])->one();
        $vip = [];
        if(!empty($vipList)){
            $vip = [
                'good_id'=>$vipList->id,
                'pic'=>Yii::$app->params['img_path'].$vipList->pic,
                'name'=>$vipList->name,
                'number'=>$vipList->number,
                'volum'=>$vipList->volum,
                'sale_price'=>$vipList->vip_price,
                'original_price'=>$vipList->price,
                'unit'=>$vipList->unit,
                'type'=>2,
                'operate'=>0,
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
     * 会员充值页面商品大类接口
     */
    public function actionVipType(){
        $types = GoodType::find()->joinWith('goodInfos')->addSelect(['good_type.id as id','good_type.name as name',"CONCAT('".Yii::$app->params['img_path']."',logo) as logo"])->where(['good_type.is_active'=>1])->all();
        $type = ArrayHelper::getColumn($types,function($element){
            return [
                'id'=>$element->id,
                'name'=>$element->name,
                'logo'=>$element->logo,
            ];
        });
        return $this->showResult(200,'成功',$type);
    }

    /**
     * 抢购列表接口
     */
    public function actionRushList(){
        //获取页数,默认为1
        $page = Yii::$app->request->post('page',1);
        $token = Yii::$app->request->post('token');
        if(!empty($token)){
            $userLogin = UserLogin::findOne(['token'=>$token]);
            if(empty($userLogin)){
                $user_id = 0;
            }else{
                $userInfo = $userLogin->userInfo;
                if(empty($userInfo)){
                    return $this->showResult(302,'用户信息状态异常');
                }
                $user_id = $userInfo->id;
            }
        }else{
            $user_id = 0;
        }
        $pageSize = Yii::$app->params['pageSize'];
        $query = GoodRush::find()->joinWith('g')->addSelect(['good_rush.*',"CONCAT($user_id) as uid"])
            ->leftJoin('merchant_info','good_info.merchant=merchant_info.id')
            ->leftJoin('good_type','good_info.type=good_type.id')
            ->where("good_rush.is_active=1 and start_at<=".time()." and 
            end_at>=".time()." and merchant_info.id>0 and good_info.is_active=1 
            and merchant_info.is_active=1 and good_info.merchant>0 and good_info.id>0 
            and good_type.id>0 and good_type.is_active=1"
            );

        $count = $query->count();
        $query->offset(($page-1)*$pageSize)->limit($pageSize);
        $goods = $query->all();
        $data = [];
        //处理获取到得数据
        if(!empty($goods)){
            $data = GoodInfo::RushData($goods);
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
        //列表入口，0首页进入 1会员充值商品页面
        $from = Yii::$app->request->post('from',0);
        $from_val = Yii::$app->request->post('from_val',0);
        //排序字段 '' 无 price价格 sale 销量
        $sort_key = Yii::$app->request->post('sort_key','');
        //排序 0默认 1升序 2降序
        $sort_val = Yii::$app->request->post('sort_val',0);
        //查询会员
        $query = GoodInfo::find()->joinWith(['merchant0','type0'])
            ->where('good_info.is_active=1 and good_info.merchant>0 and 
            merchant_info.id>0 and merchant_info.is_active=1 and 
            good_type.id>0 and good_type.is_active=1');
        if($from == 1){
            $token = Yii::$app->request->post('token');
            if(empty($token)||empty(UserLogin::findOne(['token'=>$token]))){
                return $this->showResult(401,'您的登录信息已失效，请重新登录');
            }
            $userInfo = UserLogin::findOne(['token'=>$token])->userInfo;
            if(empty($userInfo)){
                return $this->showResult(302,'用户信息状态异常');
            }
            if($userInfo->is_vip!=1){
                return $this->showResult(309,'您还不是会员，请先充值成为会员');
            }
            $query->leftJoin("(SELECT a.* FROM order_detail a LEFT JOIN order_info b ON a.oid=b.id 
            WHERE state between 2 and 7 and order_date>=".strtotime(date('Y-m-01 00:00:00',time())).") c",'good_info.id=c.gid');
            $query->addSelect(['good_info.*','IFNULL(sum(c.amount),0) as sum'])->groupBy(['good_info.id']);
            if(!empty($from_val)){
                $query->andWhere("type=$from_val");
                if($sort_key == 'price'){
                    $query->orderBy(['vip_price'=>$sort_val==1 ? SORT_ASC:SORT_DESC,'order'=>SORT_ASC]);
                }elseif ($sort_key == 'sale'){
                    $query->orderBy(['sum'=>$sort_val==1 ? SORT_ASC:SORT_DESC,'order'=>SORT_ASC]);
                }else{
                    $query->orderBy(['order'=>SORT_ASC]);
                }
            }
        }else{
            $query->andWhere("good_info.vip_show=1")->orderBy(['good_info.order'=>SORT_ASC]);
        }
        $count = $query->count();
        $query->offset(($page-1)*$pageSize)->limit($pageSize);
        $goods = $query->all();
        $data = [];
        //处理获取到得数据
        if(!empty($goods)){
            $data = GoodInfo::VipData($goods,$from);
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
        //列表入口来源 0大类 1商店 2搜索产品
        $from = Yii::$app->request->post('from',0);
        //来源值
        $from_val = Yii::$app->request->post('from_val',0);
        //大类下的小分类，先获取key
        $key = Yii::$app->request->post('key','');
        //获取小分类的value
        $value = Yii::$app->request->post('value',0);
        //页数
        $page = Yii::$app->request->post('page',1);
        //排序字段 '' 无 price价格 sale 销量
        $sortKey = Yii::$app->request->post('sort_key','');
        //排序 0默认 1升序 2降序
        $sortValue = Yii::$app->request->post('sort_val',0);
        $pageSize = Yii::$app->params['pageSize'];
        $query = GoodInfo::find()->joinWith(['merchant0','type0']);
        $query->where('good_info.is_active=1 and merchant_info.id>0 and merchant_info.is_active=1 and 
            good_info.merchant>0 and good_type.id>0 and good_type.is_active=1');;
        if($key=='price'){
            $value = explode('~',$value);
        }
        if($from == 0){//为0表示大类下列表
            if(empty($from_val)){
                return $this->showResult(301,'获取数据异常');
            }
            $query->andWhere(['type'=>$from_val]);
        }elseif($from == 1){//为1表示店铺商家下的列表
            if(empty($from_val)){
                return $this->showResult(301,'获取数据异常');
            }
            $shop = ShopInfo::findOne($from_val);
            if(empty($shop)){
                return $this->showResult(303,'未获取到店铺信息');
            }
            $query->andWhere(['merchant'=>$shop->merchant]);
        }elseif($from == 2){ //为2表示搜索产品
            if(empty($from_val)){
                return $this->showResult(301,'获取数据异常');
            }
            $query->andFilterWhere(['like','good_info.name',$from_val]);
        }else{//其他值不识别
            return $this->showResult(301,'获取数据异常');
        }
        if(!empty($key)&&!empty($value)){
            $query->andWhere(['and',$key=='price' ? "$key >= $value[0] ".(empty($value[1])||$value[1]=='+∞' ? '' :
                    "and $key <$value[1]") : "$key=$value"]);
        }
        $count = $query->count();
        //排序
        if(empty($sortKey)||empty($sortValue)){
            //默认排序
            $query->orderBy(['good_info.order'=>SORT_ASC]);
        }elseif(!empty($sortKey)&&!empty($sortValue)){
            if($sortKey=='price'){//按价格
                $query->orderBy(['good_info.price'=>$sortValue==1 ? SORT_ASC:SORT_DESC,'order'=>SORT_ASC]);
            }elseif($sortKey=='sale'){//按销量
                $query->leftJoin("(SELECT a.* FROM order_detail a LEFT JOIN order_info b ON a.oid=b.id 
            WHERE state between 2 and 7 and order_date>=".strtotime(date('Y-m-01 00:00:00',time())).") c",'good_info.id=c.gid')
                    ->addSelect(['good_info.*','IFNULL(sum(c.amount),0) as sum'])->groupBy(['good_info.id']);
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
        //获取入口type 1普通商品 2抢购 3会员
        $type = Yii::$app->request->post('type');
        //获取操作权限 不传则默认无权限
        $operate = Yii::$app->request->post('operate',0);
        //根据用户是否登录判断是否可购买会员商品
        $token = Yii::$app->request->post('token');
        //审核参数
        if(empty($good_id)||empty($type)){
            return $this->showResult(301,'获取数据异常');
        }
        $goodQuery = GoodInfo::find()->joinWith(['merchant0','type0']);
        //根据type判断是否为会员或者抢购
        if($type == 1){
            $goodQuery->where("good_info.id=$good_id");
        }elseif ($type==2){
            $goodQuery->where("good_info.id=$good_id");
            if($operate==1){
                if(empty($token)||empty(UserLogin::findOne(['token'=>$token]))){
                    return $this->showResult(401,'您的登录信息已失效，请重新登录');
                }
                $userInfo = UserLogin::findOne(['token'=>$token])->userInfo;
                if(empty($userInfo)){
                    return $this->showResult(302,'用户信息状态异常');
                }
                if($userInfo->is_vip!=1){
                    return $this->showResult(309,'您还不是会员，请先充值成为会员');
                }
            }elseif ($operate == 0 ){
                $goodQuery->andWhere("vip_show=1");
            }else{
                return $this->showResult(301,'获取数据异常');
            }
        }elseif ($type==3){
            $goodQuery->joinWith('goodRush')->where(
                "good_info.id=$good_id and good_rush.is_active=1 and good_rush.id>0 and start_at<=".time()." and end_at>=".time());
        }else{
            return $this->showResult(301,'获取数据异常');
        }
        $goodQuery->andWhere('good_info.is_active=1 and merchant>0 
            and merchant_info.id>0 and merchant_info.is_active=1 
            and good_info.type>0 and good_type.is_active=1 and good_type.id>0');
        $goodInfo = $goodQuery->one();
        //判断产品信息是否存在
        if(empty($goodInfo)){
            if($type == 1){
                $message = '未获取到该商品信息';
            }elseif ($type==2){
                $message = '未获取到该商品的会员信息';
            }else{
                $message = '未获取到该商品的抢购信息';
            }
            return $this->showResult(304,$message);
        }
        //判断商品是否已下架
        if($goodInfo->is_active!=1){
            return $this->showResult(304,'该商品已下架');
        }
        //产品轮播图;
        $pics = ArrayHelper::getColumn($goodInfo->goodPics,function($element){
            return Yii::$app->params['img_path'].$element->pic;
        });
        //查找评论   comment_detail与order_comment
        $query = CommentDetail::find()->joinWith('c')->where(['gid'=>$good_id,'comment_detail.status'=>1])->orderBy(['order_comment.add_at'=>SORT_DESC]);
        $query->offset(0)->limit(2);
        $comments = $query->all();
        $comment = CommentDetail::data($comments);
        if(empty($token)){
            $user_id = 0;
            $is_collected = 0;
            $collection_id = 0;
        }else{
            $userLogin = UserLogin::findOne(['token'=>$token]);
            if(empty($userLogin)||empty($userLogin->uid)||empty($userLogin->userInfo)){
                $user_id = 0;
                $is_collected = 0;
                $collection_id = 0;
            }else{
                $user_id = $userLogin->uid;
                //判断该商品是否已收藏
                $collectedGood = GoodCollection::find()->where(['uid'=>$userLogin->uid,'gid'=>$good_id,'status'=>1])->one();
                if(!empty($collectedGood)){
                    $is_collected = 1;
                    $collection_id = $collectedGood->id;
                }else{
                    $is_collected = 0;
                    $collection_id = 0;
                }
            }
        }
        if($type == 1){
            $payArr = explode('|',$goodInfo->original_pay);
        }elseif($type == 2){
            $payArr = explode('|',$goodInfo->vip_pay);
        }else{
            $payArr = explode('|',$goodInfo->goodRush->rush_pay);
        }
        //处理详情
        $details = $goodInfo->orderDetails;
        $sale = array_sum(ArrayHelper::getColumn($details,'amount'));
        $data = [
            'good_id'=>$goodInfo->id,
            'pics'=>$pics,
            'name'=>$goodInfo->name,
            'volum'=>$goodInfo->volum,
            'number'=>$goodInfo->number,
            'sale_price'=>$type==1 ? $goodInfo->pro_price:($type==2 ? $goodInfo->vip_price:$goodInfo->goodRush->price),
            'original_price'=>$goodInfo->price,
            'unit'=>$goodInfo->unit,
            'sale'=>$sale,
            'comments'=>$comment,
            'type'=>$type,
            'point_sup'=>$type==3 ? $goodInfo->goodRush->point_sup:$goodInfo->point_sup,
            'cash_pay'=>in_array(1,$payArr) ? 1:0,
            'ali_pay'=>in_array(2,$payArr) ? 1:0,
            'we_pay'=>in_array(3,$payArr) ? 1:0,
            'operate'=>$operate,
            'is_collected'=>$is_collected,
            'collection_id'=>$collection_id,
            'detail'=>'http://120.25.144.153/wine/admin/web/index.php/good/info?id='.$goodInfo->id,
        ];
        if($type == 3){
            $rush = $goodInfo->goodRush;
            if(!empty($user_id)){
                $order = OrderDetail::find()->joinWith('o')->addSelect(["SUM(amount) as sum"])
                    ->where("type=3 and state between 2 and 7 and uid=$user_id and gid=".$good_id." and 
                        rush_id= $rush->id and order_date>=$rush->start_at and order_date<=$rush->end_at")->one();
                $buyNum =$order->sum;
                $rest = $rush->limit-$buyNum;
            }else{
                $rest = $rush->limit;
            }
            $data['limit'] = $rush->limit;
            $data['rest'] = $rest<0 ? 0:$rest;
            $data['amount'] = $rush->amount;
            $data['end_at'] = date('Y-m-d H:i:s',$rush->end_at);
        }
        return $this->showResult(200,'详情如下',$data);
    }

    public function actionHotSearch(){
        $search = HotSearch::find()->orderBy(['order'=>SORT_ASC])->all();
        $data = [];
        if(!empty($search)){
            $data = ArrayHelper::getColumn($search,function($element){
                return ['name'=>$element->name];
            });
        }
        return $this->showResult(200,'成功',$data);
    }
}