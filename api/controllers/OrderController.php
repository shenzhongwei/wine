<?php
namespace api\controllers;

use api\models\AccountInout;
use api\models\GoodInfo;
use api\models\GoodRush;
use api\models\OrderDetail;
use api\models\OrderInfo;
use api\models\PromotionInfo;
use api\models\ShoppingCert;
use api\models\UserAddress;
use api\models\UserInfo;
use api\models\UserPromotion;
use api\models\UserTicket;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: szw
 * Date: 2016/8/25
 * Time: 15:41
 */

class OrderController extends ApiController{

    /**
     * @return array
     * @throws \yii\db\Exception
     * 确认订单接口
     */
    public function actionOrder(){
        $user_id = Yii::$app->user->identity->getId();
        $userInfo = UserInfo::findOne($user_id);
        if(empty($userInfo)){
            return $this->showResult(302,'未找到该用户');
        }
        $from = Yii::$app->request->post('from');//订单入口 1直接购买 2购物车购买
        /*
         * 类型 必传
         * 当from=1 表示直接购买时 有三种 1普通商品 2会员 3抢购
         * 当from=2 表示购物车时 有2种 1普通购物车 2会员购物车
         */
        $type = Yii::$app->request->post('type',1);
        //订单产品参数[{"good_id":2,"amount":2,"unit_price":245.00},{"good_id":3,"amount":3,"unit_price":100.00}]
        $from_val = json_decode(stripcslashes( Yii::$app->request->post('from_val','')),true);
        $shop_id = Yii::$app->request->post('shop_id');//商店id
        $send_bill = Yii::$app->request->post('send_bill');//运费
        $total_price = Yii::$app->request->post('total_price');//总价
        $ticket_id = Yii::$app->request->post('ticket_id',0);//优惠券id
        $point = Yii::$app->request->post('point',0);//使用的积分
        $pay_mode = Yii::$app->request->post('pay_mode');//付款方式1余额 2支付宝 3微信
        $pay_price = Yii::$app->request->post('pay_price');//付款价格
        $address_id = Yii::$app->request->post('address_id');//收货地址id
        //验证参数
        if(empty($from_val)|| empty($shop_id)||empty($total_price)||
            empty($pay_mode)||empty($pay_price)||empty($from)||
            empty($address_id)||empty($type)){
            return $this->showResult(301,'读取订单信息失败');
        }
        //验证用户购买会员商品时的身份信息
        if($type == 2 && $userInfo->is_vip==0){
            return $this->showResult(309,'非会员无法用结算会员商品');
        }
        //验证抢购
        if($type==3&&$from==2){
            return $this->showResult(303,'抢购商品无法通过购物车结算');
        }
        //验证地址
        $userAddress = UserAddress::find()->where("lat>0 and lng>0 and uid=$user_id and id=$address_id and status=1")->one();
        if(empty($userAddress)){
            return $this->showResult(303,'用户地址信息异常');
        }
        //验证商品总价格和付款价格以及购物车信息是否有效
        $total=0;
        $payStr = $pay_mode==1 ? '余额付款':($pay_mode==2 ? '支付宝支付':'微信支付');
        foreach($from_val as $value){
            if(empty($value['unit_price']||empty($value['good_id'])||empty($value['amount']))){
                return $this->showResult(304,'未读取到商品信息');
            }
            $goodInfo = GoodInfo::findOne($value['good_id']);
            if(empty($goodInfo)){
                return $this->showResult(304,'商品信息异常');
            }
            if($goodInfo->is_active != 1){
                return $this->showResult(304,$goodInfo->name.'商品已下架，无法下单');
            }
            if($from ==2 ){
                $userCert = ShoppingCert::findOne(['gid'=>$value['good_id'],'amount'=>$value['amount'],'uid'=>$user_id,'type'=>$type]);
                if(empty($userCert)){
                    return $this->showResult(304,'购物车信息错误');
                }
            }
            //验证各类型下的商品信息
            if($type == 1){
                if($goodInfo->pro_price!=$value['unit_price']){
                    return $this->showResult(304,$goodInfo->name.'价格异常');
                }
                $payArr = explode('|',$goodInfo->original_pay);
                $point_sup = $goodInfo->point_sup;
            }elseif ($type==2){
                if($goodInfo->vip_price!=$value['unit_price']){
                    return $this->showResult(304,$goodInfo->name.'价格异常');
                }
                $payArr = explode('|',$goodInfo->original_pay);
                $point_sup = $goodInfo->point_sup;
            }elseif ($type==3){
                $goodRush = GoodRush::find()->where("gid=".$value['good_id']."and is_active=1 and start_at<=".time()." and end_at>=".time())->one();
                if(empty($goodRush)){
                    return $this->showResult(304,$goodInfo->name.'不是抢购商品，无法下单');
                }
                if($goodRush->amount <= 0){
                    return $this->showResult(304,'该抢购商品已经没有库存');
                }
                if($goodRush->amount<$value['amount']){
                    return $this->showResult(304,'该抢购商品剩余库存不足');
                }
                $order = OrderDetail::find()->joinWith('o')->addSelect(["SUM(amount) as sum"])
                    ->where("type=3 and state between 2 and 7 and uid=$user_id and gid=".$value['good_id']." and rush_id=$goodRush->id and
                    order_date>=$goodRush->start_at and order_date<=$goodRush->end_at")->one();
                $buyNum =$order->sum;
                if($buyNum>=$goodRush->limit){
                    return $this->showResult(304,'您没有可购买的数量');
                }
                if(($buyNum+$value['amount'])>=$goodRush->limit){
                    return $this->showResult(304,'您购买的数量超出可购买的数量');
                }
                if($goodRush->price!=$value['unit_price']){
                    return $this->showResult(304,$goodInfo->name.'价格异常');
                }
                $payArr = explode('|',$goodRush->rush_pay);
                $point_sup = $goodRush->point_sup;
            }else{
                return $this->showResult(301,'数据异常');
            }
            if(!in_array($pay_mode,$payArr)){
                return $this->showResult(301,$goodInfo->name.'不支持'.$payStr);
            }
            if($point>0 && $point_sup==1){
                return $this->showResult(301,$goodInfo->name.'不支持使用积分');
            }
            $total += ($value['unit_price']*$value['amount']);
        }
        if($total_price!=$total||($pay_price-$send_bill)>$total_price){
            return $this->showResult(303,'系统异常');
        }
        //验证优惠券是否是该用户的
        if(!empty($ticket_id)){
            $userTicket = UserTicket::findOne(['uid'=>$user_id,'id'=>$ticket_id,'status'=>1]);
            if(empty($userTicket)){
                return $this->showResult(304,'优惠券信息异常');
            }
        }
        /**
         * 用事务进行数据库处理
         * 1、保存订单信息
         * 2、保存订单的商品信息
         * 3、判断是否是购物车下单，若是则将购物车中相应订单删除
         * 4、判断是否使用优惠券
         */
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $order = new OrderInfo();
            $order->attributes = [
                'sid'=>$shop_id,
                'uid'=>$user_id,
                'aid'=>$address_id,
                'order_date'=>time(),
                'order_code'=>OrderInfo::generateCode().date('YmdHis').$user_id,
                'pay_id'=>$pay_mode,
                'total'=>$total_price,
                'type'=>$type,
                'discount'=>$total_price-$pay_price+$send_bill,
                'send_bill'=>$userInfo->is_vip ? 0:$send_bill,
                'ticket_id'=>empty($ticket_id) ? 0:$ticket_id,
                'point'=>empty($point) ? 0:$point,
                'pay_bill'=>$pay_price,
                'state'=>1,
            ];
            if(!$order->save()){
                throw new Exception('保存订单基本信息出错');
            }
            foreach($from_val as $value){
                $detail = new OrderDetail();
                $detail->attributes = [
                    'oid'=>$order->id,
                    'gid'=>$value['good_id'],
                    'amount'=>$value['amount'],
                    'single_price'=>$value['unit_price'],
                    'total_price'=>$value['unit_price']*$value['amount'],
                ];
                if($type==3){
                    //保存抢购信息
                    $detail->rush_id = $goodRush->id;
                    //更新库存
                    $goodRush->amount  = $goodRush->amount-$value['amount'];
                    if(!$goodRush->save()){
                        throw new Exception('保存抢购信息出错');
                    }
                }
                if(!$detail->save()){
                    throw new Exception('保存订单商品信息出错');
                }
            }
            if($from == 2){
                $goodIds = '('.implode(',',array_column($from_val,'good_id')).')'; //array_column() 返回输入数组中某个单一列的值。
                $sql = "DELETE FROM shopping_cert WHERE gid IN $goodIds AND uid=$user_id AND type=$type";
                $row = Yii::$app->db->createCommand($sql)->execute();
                if(empty($row)){
                    throw new Exception('购物车信息异常');
                }
            }
            if(!empty($ticket_id)){
                $userTicket->status = 2;
                if(!$userTicket->save()){
                    throw new Exception('优惠券数据异常');
                }
            }
            $transaction->commit();
            return $this->showResult(200,'下单成功',$order->order_code);
        }catch (Exception $e){
            $transaction->rollBack();
            return $this->showResult(400,$e->getMessage());
        }
    }

    /**
     * @return array
     * 订单列表
     */
    public function actionOrderList(){
        $user_id = Yii::$app->user->identity->getId();
        $state = Yii::$app->request->post('state',0);//订单状态，0全部 1未付款 2待收货 6待评价
        $page = Yii::$app->request->post('page',1);//页数
        $pageSize = Yii::$app->params['pageSize'];
        $res = OrderInfo::AutoCancelOrder($user_id);
        if($res){
            //找用户订单,查找用户一个月内的订单
            $query = OrderInfo::find()->joinWith('orderDetails')->where([
                'and','uid='.$user_id,'is_del=0','order_date+2592000>'.time(),'state in (1,2,3,4,5,6,7,99)','is_del=0']);
//            var_dump($query);
//            exit;
            if(!empty($state)){//筛选
                if($state == 2){
                    $query->andWhere(['and','state between 2 and 5']);
                }else{
                    $query->andWhere(['and','state='.$state]);
                }
            }
            $count = $query->groupBy(['oid'])->count();//总数
            $query->orderBy(['order_info.order_date'=>SORT_DESC]);
            $query->offset(($page-1)*$pageSize)->limit($pageSize);
            $orders = $query->all();
            $data = [];
            //处理查找到的数据
            if(!empty($orders)){
                $data = ArrayHelper::getColumn($orders,function($element){
                    return [
                        'order_id'=>$element->id,
                        'send_bill'=>$element->send_bill,
                        'state'=>$element->state,
                        'pay_price'=>$element->pay_bill,
                        'order_date'=>date('Y-m-d H:i:s',$element->order_date),
                        'order_code'=>$element->order_code,
                        'pay_mode'=>$element->pay_id,
                        'detail'=>ArrayHelper::getColumn($element->orderDetails,function($detail){
                            return [
                                'good_id'=>$detail->gid,
                                'name'=>$detail->g->name,
                                'volum'=>$detail->g->volum,
                                'pic'=>Yii::$app->params['img_path'].$detail->g->pic,
                                'number'=>$detail->g->number,
                                'unit_price'=>$detail->single_price,
                                'original_price'=>$detail->g->price,
                                'unit'=>$detail->g->unit,
                                'amount'=>$detail->amount,
                                'total_price'=>$detail->total_price,
                            ];
                        }),
                    ];
                });
            }
            return $this->showList(200,'订单列表如下',$count,$data);
        }else{
            return $this->showResult(400,'服务器异常');
        }
    }



    /**
     * 确认收货订单接口
     */
    public function actionConfirmOrder(){
        $user_id = Yii::$app->user->identity->getId();
        $order_id = Yii::$app->request->post('order_id');//获取订单id
        if(empty($order_id)){//判断是否获取到id
            return $this->showResult(301,'读取订单信息失败');
        }
        $userOrder = OrderInfo::find()->where(['and','uid='.$user_id,'id='.$order_id,'state in (2,3,4,5)'])->one();//查找订单2-5为可收货状态
        if(empty($userOrder)){
            return $this->showResult(304,'订单数据异常，请重试');
        }
        $userOrder->state = 6;//修改字段
        if(!$userOrder->save()){
            return $this->showResult(400,'确认收货失败');
        }else{
            return $this->showResult(200,'确认收货成功');
        }
    }


    /**
     * 删除订单接口
     */
    public function actionDel(){
        $user_id = Yii::$app->user->identity->getId();
        $order_id = Yii::$app->request->post('order_id');//获取订单id
        if(empty($order_id)){//判断是否获取到id
            return $this->showResult(301,'读取订单信息失败');
        }
        $userOrder = OrderInfo::findOne(['uid'=>$user_id,'id'=>$order_id]);//查找订单
        if(empty($userOrder)||$userOrder->state<6){
            return $this->showResult(304,'订单数据异常，请重试');
        }
        $userOrder->is_del = 1;//修改字段
        if(!$userOrder->save()){
            return $this->showResult(400,'删除失败');
        }else{
            return $this->showResult(200,'删除成功');
        }
    }

    /**
     * 取消订单接口
     */
    public function actionCancel(){
        $user_id = Yii::$app->user->identity->getId();
        $order_id = Yii::$app->request->post('order_id');//获取订单id
        if(empty($order_id)){//判断是否获取到id
            return $this->showResult(301,'读取订单信息失败');
        }
        $userOrder = OrderInfo::findOne(['uid'=>$user_id,'id'=>$order_id]);//查找订单
        if(empty($userOrder)||$userOrder->state>1){//判断订单状态
            return $this->showResult(304,'订单数据异常，请重试');
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            //还原优惠券
            if(!empty($userOrder->ticket_id)){
                $ticket_id = $userOrder->ticket_id;
                $user_ticket = UserTicket::findOne(['id'=>$ticket_id,'uid'=>$user_id]);
                if(!empty($user_ticket)){
                    $user_ticket->status = 1;
                    if(!$user_ticket->save()){
                        throw new Exception('修改订单状态失败');
                    }
                }
            }
            $userOrder->state = 100;//修改字段
            $userOrder->ticket_id = 0;
            if(!$userOrder->save()){
                throw new Exception('取消订单失败');
            }
            $transaction->commit();
            return $this->showResult(200,'取消订单成功');
        }catch (Exception $e){
            $transaction->rollBack();
            return $this->showResult(400,$e->getMessage());
        }
    }

    /**
     * 订单详情接口
     * 暂无
     */


    /**
     * 订单物流状态接口
     */
    public function actionSendStatus(){
        $user_id = Yii::$app->user->identity->getId();
        $order_id = Yii::$app->request->post('order_id');
        if(empty($order_id)){//判断是否获取到id
            return $this->showResult(301,'读取订单信息失败');
        }
        $userOrder = OrderInfo::findOne(['uid'=>$user_id,'id'=>$order_id]);//查找订单
        if(empty($userOrder)||$userOrder->state<2||$userOrder->state>6||empty($userOrder->orderDetails)){//判断订单状态
            return $this->showResult(304,'订单数据异常，请重试');
        }
        //数据处理
        $data =  [
            'order_id'=>$userOrder->id,
            'state'=>$userOrder->state,
            'service_phone'=>Yii::$app->params['servicePhone'],
            'send_code'=>$userOrder->send_code,
            'order_code'=>$userOrder->order_code,
            'send_person'=>empty($userOrder->send_id) ? '未配送':(empty($userOrder->send) ? '数据丢失':$userOrder->send->name),
            'send_phone'=>empty($userOrder->send_id) ? '未配送':(empty($userOrder->send) ? '数据丢失':$userOrder->send->phone),
            'address'=>empty($userOrder->a) ? '丢失':
                $userOrder->a->province.$userOrder->a->city.$userOrder->a->district.$userOrder->a->region.$userOrder->a->address,
            'detail'=>ArrayHelper::getColumn($userOrder->orderDetails,function($detail){
                return [
                    'good_id'=>$detail->gid,
                    'name'=>$detail->g->name,
                    'volum'=>$detail->g->volum,
                    'number'=>$detail->g->number,
                    'unit_price'=>$detail->single_price,
                    'original_price'=>$detail->g->price,
                    'unit'=>$detail->g->unit,
                    'amount'=>$detail->amount,
                    'total_price'=>$detail->total_price,
                ];
            }),
        ];
        return $this->showResult(200,'订单物流状态如下',$data);
    }

    /**
     * 充值接口
     */
    public function actionAccount(){
        $user_id = Yii::$app->user->identity->getId();
        $bill = Yii::$app->request->post('bill'); //充值金额
        $userInfo = UserInfo::findOne($user_id);
        if(empty($userInfo)){
            return $this->showResult(301,'用户信息异常');
        }
        $inout = new AccountInout();
        $inout->attributes = [
            'aio_date'=>time(),
            'type'=>4,
            'target_id'=>$user_id,
            'sum'=>$bill,
            'discount'=>0,
            'status'=>2,
            'note'=>'用户'.$userInfo->nickname.'于'.date('Y年m月d日 H时i分s秒').'提交充值，未付款',
        ];
        if($inout->save()){
            return $this->showResult(200,'下单成功',time().$inout->id);
        }else{
            return $this->showResult(400,'系统异常，下单失败');
        }
    }


    /**
     * 充值页面充值金额选项以及优惠描述接口
     */
    public function actionActivity(){
        //先查找充值赠送的描述
        $vipPromotion = PromotionInfo::find()->joinWith('pt')->where(
            "promotion_type.is_active=1 and promotion_info.is_active=1 and ((start_at<=".time(). " 
            and end_at>=".time().") or (end_at=0 and start_at=0)) and `group`=3 and `condition`>0")->one();
        $billLabels = [];
        if(empty($vipPromotion)){
            $vip_des = '';
        }else{
            $cond = (int)($vipPromotion->condition);
            $condition =  $cond == $vipPromotion->condition ? $cond:$vipPromotion->condition;
            $billLabels [] = $condition;
            $vip_des = '充值满'.$condition.'元，获得终生会员资格';
        }
        //在查找充值送优惠的描述
        $bill_des = [];
        $billPromotion = PromotionInfo::find()->joinWith('pt')->where(
            "promotion_type.is_active=1 and promotion_info.is_active=1 and ((start_at<=".time(). " 
            and end_at>=".time().") or (end_at=0 and start_at=0)) and `group`=2 and env=6 and style=2")->one();
        if(!empty($billPromotion)){
            $dis = (int)($billPromotion->discount);
            $discount = $dis == $billPromotion->discount ? $dis:$billPromotion->discount;
            $bill_des[] = "充值返回$discount"."倍积分";
        }else{
            $billPromotions = PromotionInfo::find()->joinWith('pt')->where(
                "promotion_type.is_active=1 and promotion_info.is_active=1 and ((start_at<=".time(). " 
            and end_at>=".time().") or (end_at=0 and start_at=0)) and `group`=2 and env=6 and style=1")
                ->orderBy(['`condition`'=>SORT_DESC])->all();
            if(!empty($billPromotion)){
                foreach($billPromotions as $promotion){
                    $cond = (int)($vipPromotion->condition);
                    $condition =  $cond == $promotion->condition ? $cond:$promotion->condition;
                    $dis = (int)($promotion->discount);
                    $discount = $dis == $promotion->discount ? $dis:$promotion->discount;
                    $bill_des[] = "充值$condition"."送$discount"."积分。";
                    $billLabels [] = $condition;
                }
            }
        }
        if(empty($bill_des)&&empty($vip_des)){
            return $this->showResult(301,'暂无充值活动');
        }
//        $billLabels = array_values(array_unique($billLabels));
        $data = [
            'bill_label'=>$billLabels,
            'vip'=>$vip_des,
            'bill'=>$bill_des,
        ];
        return $this->showResult(200,'充值活动如下',$data);
    }


    /**
     * 充值金额与赠送金额
     * 根据用户传递的金额判断赠送多少积分
     */
    public function actionPreBill(){
        $user_id = Yii::$app->user->identity->getId();
        $bill = Yii::$app->request->post('bill');
        $user = UserInfo::findOne($user_id);//查找用户信息
        if(empty($user)){
            return $this->showResult(302,'用户信息异常，请重试');
        }
        //判断参数是否传递
        if(!isset($bill)){
            return $this->showResult(301,'读取信息出错');
        }
        //在查找充值送优惠的数据
//        $query = PromotionInfo::find()->joinWith('pt')->leftJoin(
//            "(SELECT count(*) as num,pid FROM user_promotion WHERE uid=$user_id AND status=1 GROUP BY pid) c","c.pid=promotion_info.id")
//            ->where("promotion_type.is_active=1 and promotion_info.is_active=1 and ((start_at<=".time(). " and end_at>=".time().")
//            or (end_at=0 and start_at=0)) and env=6 and `group`=2 and ((style=2) or (style=1 and `condition`<=".$bill."))");
//        $query->select(["promotion_info.*",'promotion_type.group as group','IFNULL(c.num,0) as num']);
//        $query->orderBy(['`condition`'=>SORT_DESC]);
//        $query->having("time=0 or time>num");
//        $billPromotion = $query->one();
        $billPromotion='';
        /*
         * 进行判断，1若存在则加上，不存在则为原金额
         */
        if(empty($billPromotion)){
            $pre = 0;
        }else{
            $pre = 0;
        }
        $data = [
            'pre_bill'=>$bill+$pre,
        ];
        return $this->showResult(200,'成功',$data);
    }

    /**
     * 我的页面订单数量
     */
    public function actionOrderNum(){
        $user_id = Yii::$app->user->identity->getId();
        $payCount = OrderInfo::find()->where("uid=$user_id and is_del=0")->andWhere('state=1')->count();
        $receiveCount = OrderInfo::find()->where("uid=$user_id and is_del=0")->andWhere('state between 2 and 5')->count();
        $commentCount = OrderInfo::find()->where("uid=$user_id and is_del=0")->andWhere('state=6')->count();
        $data = [
            'pay'=>$payCount,
            'receive'=>$receiveCount,
            'comment'=>$commentCount,
        ];
        return $this->showResult(200,'成功',$data);
    }

}