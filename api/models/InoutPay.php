<?php

namespace api\models;

use common\pay\alipay\helpers\AlipayHelper;
use common\pay\wepay\helpers\Log;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "inout_pay".
 *
 * @property integer $id
 * @property integer $inout_id
 * @property integer $uid
 * @property integer $pay_date
 * @property integer $pay_id
 * @property string $account
 * @property string $out_trade_no
 * @property string $transaction_id
 * @property string $money
 * @property integer $status
 *
 * @property AccountInout $inout
 */
class InoutPay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inout_pay';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inout_id', 'uid', 'pay_date', 'pay_id', 'status'], 'integer'],
            [['money'], 'number'],
            [['account', 'out_trade_no'], 'string', 'max' => 64],
            [['transaction_id'], 'string', 'max' => 32],
            [['inout_id'], 'exist', 'skipOnError' => true, 'targetClass' => AccountInout::className(), 'targetAttribute' => ['inout_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'inout_id' => '明细id',
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
    public function getInout()
    {
        return $this->hasOne(AccountInout::className(), ['id' => 'inout_id']);
    }

    public static function Pay($params){
        //根据付款id调用log
        if($params['pay_id']==3){
            $log = new Log();
        }elseif($params['pay_id'==2]){
            $log = new AlipayHelper();
        }else{
            return false;
        }
        $log->log_result('开启事务');
        //开始事务..
        $transaction = Yii::$app->db->beginTransaction();
        try{
            //判断订单状态
            $inout_id = substr($params['order_code'],10);
            $inout = AccountInout::findOne(['id'=>$inout_id,'type'=>4,'status'=>2]);
            if(empty($inout)){
                throw new Exception('订单不存在',301);
            }
            //判断是否已付款
            $inoutPay = self::findOne(['inout_id'=>$inout->id,'status'=>1]);
            if(!empty($inoutPay)){
                throw new Exception('该订单已付款',200);
            }
            //判断金额是否正确
            if(($params['pay_money']) !=$inout->sum){
                throw new Exception('付款金额错误',304);
            }
            //数据库操作 0修改用户钱包 1修改订单状态 2存入付款信息 3保存系统账户信息 4根据金额判断用户是否可开通会员 5保存用户使用优惠信息
            $userAccount = UserAccount::findOne(['target'=>$inout->target_id,'type'=>1,'level'=>2]);
            if(empty($userAccount)){
                $userAccount = new UserAccount();
                $userAccount->create_at = time();
                $userAccount->start = 0;
                $userAccount->end = $inout->sum+$inout->discount;
            }else{
                $userAccount->start = $userAccount->end;
                $userAccount->end = $userAccount->start+$inout->sum+$inout->discount;
            }
            $userAccount->target=$inout->target_id;
            $userAccount->level=2;
            $userAccount->type=1;
            $userAccount->is_active = 1;
            if(!$userAccount->save()){
                throw new Exception('保存用户余额账户信息出错',400);
            }
            $inout->status = 1;
            $inout->aid = $userAccount->id;
            if(!$inout->save()){
                throw new Exception('更改用户充值信息出错',400);
            }
            $payInfo = new InoutPay();
            $payInfo->attributes=[
                'inout_id' => $inout->id,
                'uid' => $inout->target_id,
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
                'type'=>4,
                'target_id'=>$inout->id,
                'sum'=>$params['pay_money'],
                'discount'=>0,
                'status'=>1,
            ];
            if(!$sysInout->save()){
                throw new Exception('保存系统账户明细出错',400);
            }
            $user_id = $inout->target_id;
            $userInfo = UserInfo::findOne($user_id);
            if($userInfo->is_vip==0){
                $promotion = PromotionInfo::findOne(['pt_id'=>3,'target_id'=>1,'is_active'=>1]);
                if(!empty($promotion)){
                    if($promotion->condition<=$params['pay_money']){
                        $userInfo->is_vip=1;
                        if(!$userInfo->save()){
                            throw new Exception('更改用户会员状态出错',400);
                        }
                        $message = new MessageList();
                        $message->attributes = [
                            'type_id'=>2,
                            'title'=>'会员开通成功',
                            'content'=>'恭喜您成功充值'.$params['pay_money'].'元并成功开通双天会员，各种优惠等着你',
                            'own_id'=>$user_id,
                            'target'=>14,
                            'status'=>0,
                            'publish_at'=>date('Y-m-d')
                        ];
                        if(!$message->save()){
                            throw new Exception('生成用户开通会员消息出错');
                        }
                    }
                }
            }
            if($inout->discount>0){
                $usedPromotion = PromotionInfo::find()->where([
                    'and','discount='.$inout->discount,'condition<='.$params['pay_money'],
                    'pt_id=2','is_active=1','start_at<='.time(),'end_at>='.time()
                ])->one();
                if(!empty($usedPromotion)){
                    $userPromotion = new UserPromotion();
                    $userPromotion->attributes = [
                        'uid'=>$inout->target_id,
                        'type'=>2,
                        'target_id'=>$inout->id,
                        'pid'=>$usedPromotion->id,
                        'add_at'=>time(),
                        'status'=>1
                    ];
                    if(!$userPromotion->save()){
                        throw new Exception('保存用户使用优惠信息出错',400);
                    }
                }
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
