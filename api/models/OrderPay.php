<?php

namespace api\models;

use common\pay\alipay\helpers\AlipayHelper;
use common\pay\wepay\helpers\Log;
use daixianceng\smser\Wxtsms;
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

    public function validateMobilePhone($mobilephone){
        return preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9][0-9]{8}|17[0-9]{9}$|14[0-9]{9}$/",$mobilephone) && strlen($mobilephone)==11;
    }

    public static function Pay($params){
        //根据付款id调用log
        if($params['pay_id']==3){
            $log = new Log();
        }elseif($params['pay_id']==2){
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
            if(($params['pay_money']) !=$orderInfo->pay_bill){
                throw new Exception('付款金额错误',304);
            }
            //数据库操作 0判断是否有推荐人优惠可用 1修改订单状态 2存入付款信息 3修改系统账户金额
            $userInfo = UserInfo::findOne($orderInfo->uid);
            if(empty($userInfo)){
                throw new Exception('未找到该用户信息',304);
            }
            //判断推荐人
            if(!empty($userInfo->invite_user_id)){
                /*
                 * 有推荐人则
                 * 1、判断该用户是否下过单
                 * 2、若未下过单，则找出有效的推荐人活动
                 * 3、若是送券，则在用户券库中增加优惠券
                 * 4、若是送余额则为用户的账户添加对应金额，同事生成账户明细
                 */
                $historyOrder = OrderInfo::find()->where(['and','uid='.$orderInfo->uid,'state>2','state<>99'])->one();//1
                if(empty($historyOrder)){//2
                    $promotion = PromotionInfo::find()->where('
                    pt_id in (5,6) and ((start_at<='.time().' and end_at>='.time().') or (start_at=0 and end_at=0)) and is_active=1')->one();
                    if(!empty($promotion)){
                        $count = UserPromotion::find()->where(['pid'=>$promotion->id,'uid'=>$userInfo->invite_user_id,'status'=>1,'type'=>3])->count();
                        if($count<$promotion->time||$promotion->time=0){
                            $message = new MessageList();
                            if($promotion->pt_id==5){
                                $userTicket = new UserTicket();
                                $userTicket->attributes = [
                                    'uid'=>$userInfo->id,
                                    'pid'=>$promotion->id,
                                    'start_at'=>empty($promotion->valid_circle) ? 0:time(),
                                    'end_at'=>empty($promotion->valid_circle) ? 0:time()+($promotion->valid_circle*86400),
                                    'status'=>1,
                                ];
                                if(!$userTicket->save()){
                                    throw new Exception('保存推荐人优惠券出错',304);
                                }
                                $content = '用户'.$userInfo->phone.'通过您的邀请成功下单，特此赠送您'.$promotion->discount.'元面值优惠券，点击此处查看';
                                $target = 11;
                            }else{
                                $userAccount = UserAccount::findOne(['target'=>$userInfo->invite_user_id,'type'=>1,'level'=>2]);
                                if(empty($userAccount)){
                                    $userAccount = new UserAccount();
                                    $userAccount->create_at = time();
                                    $userAccount->start = 0;
                                    $userAccount->end = $promotion->discount;
                                }else{
                                    $userAccount->start = $userAccount->end;
                                    $userAccount->end = $userAccount->start+$promotion->discount;
                                }
                                $userAccount->target=$userInfo->invite_user_id;
                                $userAccount->level=2;
                                $userAccount->type=1;
                                $userAccount->is_active = 1;
                                if(!$userAccount->save()){
                                    throw new Exception('保存用户余额账户信息出错',400);
                                }
                                $accountInout = new AccountInout();
                                $accountInout->attributes = [
                                    'aid'=>$userAccount->id,
                                    'aio_date'=>time(),
                                    'type'=>3,
                                    'target_id'=>$promotion->id,
                                    'sum'=>0,
                                    'discount'=>$promotion->discount,
                                    'status'=>1
                                ];
                                if(!$accountInout->save()){
                                    throw new Exception('保存用户余额账户明细出错',400);
                                }
                                $content = '用户'.$userInfo->phone.'通过您的邀请成功下单，特此赠送您'.$promotion->discount.'元至至账户余额，点击此处查看';
                                $target = 12;
                            }
                            $message->attributes = [
                                'type_id'=>2,
                                'title'=>'您的推荐人下单成功',
                                'content'=>$content,
                                'own_id'=>$userInfo->invite_user_id,
                                'target'=>$target,
                                'status'=>0,
                                'publish_at'=>date('Y-m-d')
                            ];
                            $userPromotion = new UserPromotion();
                            $userPromotion->attributes = [
                                'uid'=>$userInfo->invite_user_id,
                                'type'=>3,
                                'target_id'=>$orderInfo->id,
                                'pid'=>$promotion->id,
                                'add_at'=>time(),
                                'status'=>1,
                            ];
                        }
                    }
                }
            }
            $orderInfo->state=2;
            $orderInfo->pay_date = time();
            $orderInfo->pay_id = $params['pay_id'];
            if(!$orderInfo->save()){
                throw new Exception('更新订单状态出错',400);
            }
            $shop=$orderInfo->s;
            if(self::validateMobilePhone($shop->phone)){
                //给店铺发短信
                $smser = new Wxtsms();
                $smser->username = Yii::$app->params['smsParams']['username'];
                $smser->setPassword(Yii::$app->params['smsParams']['password']);
                $content = "【酒双天】您有新的订单待处理，订单编号：$orderInfo->order_code，请尽快处理！";
                $res = $smser->sendSms($shop->phone,$content);
                if($res){
                    $log->log_result('短信发送成功');
                }else{
                    $log->log_result('短信发送失败');
                }
            }else{
                $log->log_result('非法的商家号码，无法发送短信');
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
            if(!$payInfo->save()){
                throw new Exception('保存付款信息出错',400);
            }
            $sysAccount = UserAccount::findOne(['target'=>1,'level'=>1,'type'=>$params['pay_id']]);
            if(empty($sysAccount)){
                $sysAccount = new UserAccount();
                $sysAccount->create_at = time();
                $sysAccount->start = 0;
                $sysAccount->end = $params['pay_money'];
            }else{
                $sysAccount->start = $sysAccount->end;
                $sysAccount->end = $sysAccount->start+$params['pay_money'];
            }
            $sysAccount->target=1;
            $sysAccount->level=1;
            $sysAccount->type=$params['pay_id'];
            $sysAccount->is_active = 1;
            if(!$sysAccount->save()){
                throw new Exception('保存系统账户信息出错',400);
            }
            $sysInout = new AccountInout();
            $sysInout->attributes = [
                'aid'=>$sysAccount->id,
                'aio_date'=>time(),
                'type'=>2,
                'target_id'=>$orderInfo->id,
                'sum'=>$params['pay_money'],
                'discount'=>0,
                'status'=>1,
            ];
            if(!$sysInout->save()){
                throw new Exception('保存系统账户明细出错',400);
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
