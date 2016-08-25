<?php
namespace api\controllers;

use api\models\OrderInfo;
use Yii;
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2016/8/25
 * Time: 15:41
 */

class OrderController extends ApiController{

    public function actionOrder(){
        $user_id = Yii::$app->user->identity->getId();
        //订单入口 0产品详情购买 1购物车购买 2抢购购买
        $from = Yii::$app->request->post('from',0);
        //订单产品参数[{"good_id":3,"amount":3,"price":100.00},{"good_id":3,"amount":3,"price":100.00}]
        $from_val = json_decode(Yii::$app->request->post('from_val',''),true);
        //商店id
        $shop_id = Yii::$app->request->post('shop_id');
        //总价
        $total_price = Yii::$app->request->post('total_price');
        //优惠券id
        $ticket_id = Yii::$app->request->post('ticket_id');
        //付款方式1余额 2支付宝 3微信
        $pay_mode = Yii::$app->request->post('pay_mode');
        $pay_price = Yii::$app->request->post('pay_price');
        if(empty($from_val)||empty($shop_id)||empty($total_price)||empty($pay_mode)||empty($pay_price)){
            return $this->showResult(301,'读取订单信息失败');
        }
        $total=0;
        foreach($from_val as $value){
            $total += $value['price'];
        }
        if($total_price!=$total){
            return $this->showResult(303,'系统异常');
        }
        $order = new OrderInfo();
        $order->attributes = [
            'sid'=>$shop_id,
            'uid'=>$user_id,
            'order_date'=>time(),
            'order_code'=>date('YmdHis').rand(10000,99999).$user_id,
            'pay_id'=>$pay_mode,
            'total'=>$total_price,
            'discount'=>$total_price-$pay_price,
        ];
    }
}