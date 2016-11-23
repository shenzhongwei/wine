<?php

namespace api\models;

use common\helpers\ArrayHelper;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "promotion_info".
 *
 * @property integer $id
 * @property integer $pt_id
 * @property integer $style
 * @property integer $limit
 * @property integer $target_id
 * @property string $name
 * @property string $condition
 * @property string $discount
 * @property integer $valid_circle
 * @property integer $start_at
 * @property integer $end_at
 * @property integer $time
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $active_at
 *
 * @property PromotionType $pt
 * @property UserTicket[] $userTickets
 * @property UserPromotion[] $userPromotions
 */
class PromotionInfo extends \yii\db\ActiveRecord
{

    public $group;
    public $num;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promotion_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pt_id', 'style', 'limit', 'target_id', 'valid_circle', 'start_at', 'end_at', 'time', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['condition', 'discount'], 'number'],
            [['name'], 'string', 'max' => 128],
            [['pt_id'], 'exist', 'skipOnError' => true, 'targetClass' => PromotionType::className(), 'targetAttribute' => ['pt_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'pt_id' => '优惠类型',
            'style' => '优惠形式',
            'limit' => '适用范围',
            'target_id' => '范围对应的id',
            'name' => '活动名称',
            'condition' => '条件',
            'discount' => '优惠',
            'valid_circle' => '有效期限 0表示永久有效 大于0表示天数',
            'start_at' => '开始时间',
            'end_at' => '结束时间',
            'time' => '使用次数 0表示无限制',
            'regist_at' => '添加时间',
            'is_active' => '是否上架',
            'active_at' => '上架状态更改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPt()
    {
        return $this->hasOne(PromotionType::className(), ['id' => 'pt_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTickets()
    {
        return $this->hasMany(UserTicket::className(), ['pid' => 'id']);
    }

    public function getUserPromotions()
    {
        return $this->hasMany(UserPromotion::className(), ['pid' => 'id']);
    }

    public static function GetPromotion($env,$user_id,$order_id=null){
        $query = self::find()->joinWith('pt')->leftJoin(
            "(SELECT count(*) as num,pid FROM user_promotion WHERE uid=$user_id AND status=1 GROUP BY pid) c","c.pid=promotion_info.id")
            ->where("promotion_type.is_active=1 and promotion_info.is_active=1 and ((start_at<=".time(). " 
            and end_at>=".time().") or (end_at=0 and start_at=0)) and discount>0 and env=$env");
        if(!empty($order_id)){
            $order = OrderInfo::findOne($order_id);
            if(!empty($order)){
                $pay_bill = $order->pay_bill;
            }
        }
        $query->select(["promotion_info.*",'promotion_type.group as group','IFNULL(c.num,0) as num']);
        $query->having("time=0 or time>num");
        if($env == 5){
            $promotions = $query->orderBy('limit')->all();
            $orderDetails = $order->orderDetails;
            $goodIdArr = ArrayHelper::getColumn($orderDetails,'gid');
            $typeArr = ArrayHelper::getColumn($orderDetails,function($element){
                return $element->g->type;
            });
            if(!empty($promotions)){
                $style = $promotions[0]->style;
                $limit = $promotions[0]->limit;
                foreach ($promotions as $key=>$val){
                    if($style!=$val->style && $limit==$val->limit&&count($goodIdArr)>1){
                        unset($promotions[$key]);
                        continue;
                    }elseif($val->limit == 3 && !in_array($val->target_id,$goodIdArr)){
                        unset($promotions[$key]);
                        continue;
                    } elseif($val->limit == 2 && !in_array($val->target_id,$typeArr)){
                        unset($promotions[$key]);
                        continue;
                    }
                    $style = $val->style;
                    $limit=$val->limit;
                }
                if(!empty($promotions)){
                    ArrayHelper::multisort($promotions,['limit','discount'],[SORT_DESC,SORT_ASC]);
                    $promotion = $promotions[0];
                }
            }
        }else{
            $promotion = $query->one();
        }
        $result = 0;
        $type = 0;
        $amount = 0;
        if(!empty($promotion)){
            if($promotion->group==1){
                $start = empty($promotion->valid_circle) ? 0:time();
                $end = empty($promotion->valid_circle) ? 0:(time()+(24*$promotion->valid_circle*60*60));
                $res = self::SaveTicket($promotion->id,$start,$end,$user_id,$env,$order_id);
                if($res['result']==1){
                    $result = 1;
                    $type = $promotion->group;
                    $amount = $promotion->discount;
                }
            }else{
                $discount=0;
                if($promotion->style==1){
                    $discount = $promotion->discount;
                }else{
                    if(!empty($pay_bill)){
                        $discount = $pay_bill*$promotion->discount/100;
                    }
                }
                if($discount>0){
                    $res = self::SavePoint($promotion->id,$user_id,$discount,$env,$order_id);
                    if($res['result']==1){
                        $result = 1;
                        $type = $promotion->group;
                        $amount = $discount;
                    }
                }
            }
        }
        $data = [
            'result'=>$result,
            'type'=>$type,
            'amount'=>$amount
        ];
        return $data;
    }

    /**
     * @param $promotion_id
     * @param $start
     * @param $end
     * @param $user_id
     * @param $env
     * @param $order_id
     * @return array
     * 记录票的存入记录
     */
    public static function SaveTicket($promotion_id,$start,$end,$user_id,$env,$order_id){
        //事务开启
        $transaction = Yii::$app->db->beginTransaction();
        try{
            //存入优惠券
            $userTicket = new UserTicket();
            $userTicket->attributes = [
                'uid'=>$user_id,
                'pid'=>$promotion_id,
                'start_at'=>$start,
                'end_at'=>$end,
                'status'=>1,
            ];
            if(!$userTicket->save()){
                throw new Exception('保存用户优惠券出错');
            }
            //存入优惠券领取记录
            $ticketInout = new TicketInout();
            $ticketInout->attributes = [
                'uid'=>$user_id,
                'tid'=>$userTicket->id,
                'regist_at'=>time(),
                'note'=>"编号为$user_id"."的用户于".date('Y年m月d日 H时i分s秒')."领取了绑定编号为$promotion_id"."活动的优惠券一张",
                'status'=>1,
            ];
            if(!$ticketInout->save()){
                throw new Exception('券的领取记录保存出错');
            }
            //存入用户使用促销记录
            $userPromotion = new UserPromotion();
            $userPromotion->attributes = [
                'uid'=>$user_id,
                'type'=>$env,
                'target_id'=>empty($order_id) ? 0 :$order_id,
                'pid'=>$promotion_id,
                'add_at'=>time(),
                'note'=>"编号为$user_id"."的用户于".date('Y年m月d日 H时i分s秒')."参与编号为$promotion_id"."的活动，并领取了编号为$userTicket->id"."的优惠券",
                'status'=>1,
            ];
            if(!$userPromotion->save()){
                throw new Exception('用户参与活动的记录保存出错');
            }
            $transaction->commit();
            $res = 1;
            $message = '操作成功';
        }catch (Exception $e){
            $transaction->rollBack();
            $res = 0;
            $message = $e->getMessage();
        }
        $data = [
            'result'=>$res,
            'message'=>$message,
        ];
        return $data;
    }

    public static function SavePoint($promotion_id,$user_id,$amount,$env,$order_id){
        $transaction = Yii::$app->db->beginTransaction();
        try{
            //存入积分点，用户未开通则重新开户
            $point = UserPoint::findOne(['uid'=>$user_id,'is_active'=>1]);
            if(empty($point)){
                $point = new UserPoint();
                $total_amount = $amount;
                $create_at = time();
            }else{
                $total_amount = $amount+(double)($point->point);
                $create_at = $point->create_at;
            }
            $point->attributes = [
                'uid'=>$user_id,
                'point'=>$total_amount,
                'is_active'=>1,
                'create_at'=>$create_at,
                'update_at'=>time(),
            ];
            if(!$point->save()){
                throw new Exception('用户积分账户保存出错');
            }
            //记录明细
            $pointInout = new PointInout();
            $pointInout->attributes = [
                'uid'=>$user_id,
                'pid'=>$point->id,
                'pio_date'=>time(),
                'pio_type'=>1,
                'amount'=>$amount,
                'oid'=>empty($order_id) ? null:$order_id,
                'note'=>"编号为$user_id"."的用户于".date('Y年m月d日 H时i分s秒')."获得了绑定编号为$promotion_id"."活动的积分$amount".",已入账",
                'status'=>1,
            ];
            if(!$pointInout->save()){
                throw new Exception('用户积分收入记录保存出错');
            }
            //存入用户使用促销记录
            $userPromotion = new UserPromotion();
            $userPromotion->attributes = [
                'uid'=>$user_id,
                'type'=>$env,
                'target_id'=>empty($order_id) ? 0 :$order_id,
                'pid'=>$promotion_id,
                'add_at'=>time(),
                'note'=>"编号为$user_id"."的用户于".date('Y年m月d日 H时i分s秒')."参与编号为$promotion_id"."的活动，并领取了$amount"."积分",
                'status'=>1,
            ];
            if(!$userPromotion->save()){
                throw new Exception('用户参与活动的记录保存出错');
            }
            $transaction->commit();
            $res=1;
            $message= '操作成功';
        }catch (Exception $e){
            $transaction->rollBack();
            $res = 0;
            $message = $e->getMessage();
        }
        $data = [
            'result'=>$res,
            'message'=>$message,
        ];
        return $data;
    }



}
