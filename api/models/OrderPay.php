<?php

namespace api\models;

use common\pay\alipay\helpers\AlipayHelper;
use common\pay\wepay\helpers\Log;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "order_pay".
 *
 * @property integer $id
 * @property integer $oid
 * @property integer $uid
 * @property integer $pay_date
 * @property integer $pay_id
 * @property string $account
 * @property string $out_trade_no
 * @property string $transaction_id
 * @property string $money
 * @property integer $status
 *
 * @property OrderInfo $o
 */
class OrderPay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_pay';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oid', 'uid', 'pay_date', 'pay_id', 'status'], 'integer'],
            [['money'], 'number'],
            [['account', 'out_trade_no'], 'string', 'max' => 64],
            [['transaction_id'], 'string', 'max' => 32],
            [['oid'], 'exist', 'skipOnError' => true, 'targetClass' => OrderInfo::className(), 'targetAttribute' => ['oid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'oid' => '订单id',
            'uid' => '用户id',
            'pay_date' => '付款时间',
            'pay_id' => '付款方式',
            'account' => '用户支付帐户（如微信openid,支付宝id)',
            'out_trade_no' => '返回的第三方订单号',
            'transaction_id' => '支付通道返回的交易流水号',
            'money' => '实际支付金额',
            'status' => '状态',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getO()
    {
        return $this->hasOne(OrderInfo::className(), ['id' => 'oid']);
    }

    public static function Pay($params){
        $params = [
            'order_code' => $callback['out_trade_no'],
            'pay_code' => $callback['transaction_id'],
            'pay_date' => $callback['time_end'],
            'pay_money' => $callback['total_fee'],
            'mch_id' => $callback['mch_id'],
            'pay_id' => 3,
        ];
        //根据付款id调用log
        if($params['pay_id']==3){
            $log = new Log();
        }elseif($params['pay_id==2']){
            $log = new AlipayHelper();
        }else{
            return false;
        }
        $log->log_result('开启事务');
        //开始事务
        $transaction = Yii::$app->db->beginTransaction();
        try{
            //判断订单状态
            $orderInfo = OrderInfo::findOne(['order_code'=>$params['order_code'],'state'=>1,'is_del'=>0]);
            if(empty($orderInfo)){
                throw new Exception('订单不存在',301);
            }
            //判断是否已付款
            $orderPay = self::findOne(['oid'=>$orderInfo->id,'status'=>1]);
            if(!empty($orderPay)){
                throw new Exception('该订单已付款',200);
            }
            //判断金额是否正确
            if(($params['pay_money']/100) !=$orderInfo->pay_bill){
                throw new Exception('付款金额错误',304);
            }
            //数据库操作 1修改订单状态 2存入付款信息
            $orderInfo->state=2;
            $orderInfo->pay_date = time();
            $orderInfo->pauy_id = $params['pay_id'];
            if(!$orderInfo->save()){
                throw new Exception('更新订单状态出错',400);
            }
            $payInfo = new OrderPay();
            $payInfo->attributes=[
                'oid' => $orderInfo->id,
                'uid' => $orderInfo->uid,
                'pay_date' => time(),
                'pay_id' => $params['pay_id'],
                'account' => $params['mch_id'],
                'out_trade_no' => $params['order_code'],
                'transaction_id' => $params['pay_code'],
                'money' => $params['pay_money'],
                'status' => 1,
            ];
            if(!$orderPay->save()){
                throw new Exception('保存付款信息出错',400);
            }
            $transaction->commit();
            $log->log_result('成功');
            return true;
        }catch (Exception $e){
            $transaction->rollBack();
            $log->log_result($e->getMessage());
            if($e->getCode() == '200'){
                return true;
            }else{
                return false;
            }
        }
    }
}
