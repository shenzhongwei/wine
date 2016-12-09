<?php

namespace api\models;

use common\JPush\PushModel;
use common\pay\alipay\helpers\AlipayHelper;
use common\pay\wepay\helpers\Log;
use daixianceng\smser\LuosimaoSmser;
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
                 * 5、查看用户是否使用积分，若使用，则存入数据库
                 */
//                $historyOrder = OrderInfo::find()->where(['and','uid='.$orderInfo->uid,'state>=2','state<99'])->one();//1
//                if(empty($historyOrder)){//2
                    $invitedUser = UserInfo::findOne($userInfo->invite_user_id);
                    if(!empty($invitedUser)&&!empty($invitedUser->userLogin)){
                        $inviteLogin = $invitedUser->userLogin;
                        $result = PromotionInfo::GetPromotion(5,$invitedUser->id,$orderInfo->id);
                        if($result['result']==1){
                            $type = $result['type'];
                            $amount = $result['amount'];
                            //存入消息
                            $message = new MessageList();
                            $message->attributes = [
                                'type_id'=>2,
                                'title'=>$type == 1 ? '推荐下单成功送优惠':'推荐下单成功送积分',
                                'content'=>"您的推荐人$userInfo->phone"."首次下单并付款成功，送您".($type == 1 ? ("一张$amount"."元优惠券"):("$amount"."积分")).'，购物省钱两不误',
                                'own_id'=>$invitedUser->id,
                                'target'=>$type == 1 ? 11:15,
                                'status'=>0,
                                'publish_at'=>date('Y-m-d')
                            ];
                            if(!$message->save()){
                                throw new Exception('生成用户消息出错');
                            }
                            if(!empty($inviteLogin->reg_id)&&!empty($inviteLogin->reg_type)){
                                $message = '有您的推荐人首次下单并付款成功啦！奖励您'.($type==1 ? ("$amount"."元优惠券"):("$amount"."积分"))."，赶快来使用吧";
                                $target = $type == 1 ? 11:15;
                                $title = '您的推荐人下单成功啦！';
                                $extra = ['target'=>$target];
                                $jpush = new PushModel();
                                $jpush->PushReg($message,$inviteLogin->reg_id,$title,$extra,$title);
                            }
                        }
                    }
//                }
            }
            $orderInfo->state=2;
            $orderInfo->pay_date = time();
            $orderInfo->pay_id = $params['pay_id'];
            if(!$orderInfo->save()){
                throw new Exception('更新订单状态出错',400);
            }
            $shop=$orderInfo->s;
            if(!empty($shop->phone)){
                //给店铺发短信
                $smser = new LuosimaoSmser();
//                $smser->username = Yii::$app->params['smsParams']['username'];
//                $smser->setPassword(Yii::$app->params['smsParams']['password']);
                $smser->setPassword(Yii::$app->params['smsParams']['api_key']);
                $content = "您有新的订单待处理，订单编号：".$orderInfo->order_code."，请尽快处理！【双天酒易购】";
                $res = $smser->send($shop->phone,$content);
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
            $sysAccount = UserAccount::findOne(['target'=>2,'level'=>1,'type'=>$params['pay_id']]);
            if(empty($sysAccount)){
                $sysAccount = new UserAccount();
                $sysAccount->create_at = time();
                $sysAccount->start = 0;
                $sysAccount->end = $params['pay_money'];
            }else{
                $sysAccount->start = $sysAccount->end;
                $sysAccount->end = $sysAccount->start+$params['pay_money'];
            }
            $sysAccount->target=2;
            $sysAccount->level=1;
            $sysAccount->type=$params['pay_id'];
            $sysAccount->is_active = 1;
            $sysAccount->update_at = time();
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
                'note'=>'用户'.$userInfo->phone.'于'.date('Y年m月d日 H时i分s秒')."完成订单单号为$orderInfo->id"."的付款.金额：¥".$params['pay_money'],
            ];
            if(!$sysInout->save()){
                throw new Exception('保存系统账户明细出错',400);
            }
            if($orderInfo->point>0){
                $userPoint = UserPoint::findOne(['uid'=>$userInfo->id,'is_active'=>1]);
                if(empty($userPoint)){
                    throw new Exception('用户积分账户异常',304);
                }
                $userPoint->point = $userPoint->point-$orderInfo->point;
                $userPoint->update_at = time();
                if(!$userPoint->save()){
                    throw new Exception('保存用户积分出错',400);
                }
                $pointInout = new PointInout();
                $pointInout->attributes = [
                    'uid'=>$orderInfo->uid,
                    'pid'=>$userPoint->id,
                    'pio_date'=>time(),
                    'pio_type'=>2,
                    'amount'=>$orderInfo->point,
                    'oid'=>$orderInfo->id,
                    'note'=>"用户$userInfo->nickname"."于".date('Y年m月d日 H时i分s秒')."，支出$orderInfo->point"."积分用于支付编号为".$params['order_code']."的订单",
                    'status'=>1,
                ];
                if(!$pointInout->save()){
                    throw new Exception('用户积分收入记录保存出错',400);
                }
            }

            //生成专属消息
            $message = new MessageList();
            $message->attributes = [
                'type_id'=>2,
                'title'=>'订单支付成功',
                'content'=>"编号为$orderInfo->order_code"."的订单支付成功",
                'own_id'=>$orderInfo->uid,
                'target'=>4,
                'status'=>0,
                'publish_at'=>date('Y-m-d'),
            ];
            if(!$message->save()){
                throw new Exception('生成用户消息出错',400);
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
