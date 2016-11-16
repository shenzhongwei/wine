<?php

namespace admin\models;

use common\helpers\ArrayHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
/**
 * OrderInfoSearch represents the model behind the search form about `admin\models\OrderInfo`.
 */
class ReportSearch extends OrderDetail
{

    public function rules()
    {
        return  [
            [['oid', 'gid','pay_id'], 'integer'],
            [['single_price', 'total_price'], 'number'],
            [['order_date','sid','good_type'],'safe'],
            [['order_code'],'string']
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public static function getOrderCode(){
        //pay_id in (2,3) and4
        $admin = Yii::$app->user->identity;
        $admin_type = $admin->wa_type;
        $admin_id = $admin->wa_id;
        $query = OrderInfo::find()->where("state between 2 and 7");
        if($admin_type==3){
            $merchant_info = MerchantInfo::findOne(['wa_id'=>$admin_id]);
            $query->andWhere("good_info.merchant=$merchant_info->id");
        }
        $orders = $query->all();
        $data = ArrayHelper::getColumn($orders,'order_code');
        return $data;
    }

    public static function getShops(){
        $admin = Yii::$app->user->identity;
        $admin_type = $admin->wa_type;
        $admin_id = $admin->wa_id;
        $query = ShopInfo::find()->joinWith('orderInfos')->where("order_info.id>0 and state between 2 and 7");
        if($admin_type==3){
            $merchant_info = MerchantInfo::findOne(['wa_id'=>$admin_id]);
            $query->andWhere("good_info.merchant=$merchant_info->id");
        }
        $shops = $query->all();
        $data = ArrayHelper::map($shops,'id','name');
        return $data;
    }

    public static function getTypes(){
        $admin = Yii::$app->user->identity;
        $admin_type = $admin->wa_type;
        $admin_id = $admin->wa_id;
        $query = OrderDetail::find()->joinWith(['o','g'])->leftJoin('good_type','good_type.id=good_info.type')->addSelect([
            'good_type.id as good_type',
            'good_type.name as type_name',
        ])->where("state between 2 and 7");
        if($admin_type==3){
            $merchant_info = MerchantInfo::findOne(['wa_id'=>$admin_id]);
            $query->andWhere("good_info.merchant=$merchant_info->id");
        }
        $types = $query->all();
        $data = ArrayHelper::map($types,'good_type','type_name');
        return $data;
    }

    public static function getGoods(){
        $admin = Yii::$app->user->identity;
        $admin_type = $admin->wa_type;
        $admin_id = $admin->wa_id;
        $query = OrderDetail::find()->joinWith(['o','g'])->addSelect([
            'gid',
            'concat(good_info.name,good_info.volum) as good_name',
        ])->where("state between 2 and 7")->groupBy('gid');
        $goods = $query->all();
        if($admin_type==3){
            $merchant_info = MerchantInfo::findOne(['wa_id'=>$admin_id]);
            $query->andWhere("good_info.merchant=$merchant_info->id");
        }
        $data = ArrayHelper::map($goods,'gid','good_name');
        return $data;
    }

    public function search($params)
    {
        $admin = Yii::$app->user->identity;
        $admin_type = $admin->wa_type;
        $admin_id = $admin->wa_id;

        //pay_id in (2,3) and4
        $query = OrderDetail::find()->joinWith(['o','g'])->leftJoin('good_type','good_type.id=good_info.type')
            ->leftJoin("
            (select sum(good_info.cost*order_detail.amount) as cost,oid from order_detail left join good_info on order_detail.gid=good_info.id group by oid) cost
            ",'cost.oid=order_detail.oid')->addSelect([
                'order_detail.*',
                'order_info.order_date as order_date',
                'order_info.order_code as order_code',
                'order_info.sid as sid',
                'order_info.pay_id as pay_id',
                'order_info.discount as discount',
                '(order_info.point/100) as point',
                'order_info.pay_bill as pay_bill',
                'good_type.id as good_type',
                '(cost.cost+order_info.send_bill) as cost',
                '(order_info.pay_bill-cost.cost-order_info.send_bill) as real_profit',
                '(order_info.pay_bill+order_info.discount+(order_info.point/100)-cost.cost-order_info.send_bill) as profit'
        ])->where("state between 2 and 7");
        if($admin_type==3){
            $manager = MerchantInfo::findOne(['wa_id'=>$admin_id]);
            if(!empty($manager)){
                $shops = ShopInfo::find()->where(['merchant'=>$manager->id])->all();
                $idArr = array_values(ArrayHelper::getColumn($shops,'id'));
                if(!empty($idArr)){
                    $query->andWhere("order_info.sid in (".implode(',',$idArr).")");
                }else{
                    $query->andWhere('order_info.sid=0');
                }
            }else{
                $query->andWhere('order_info.sid=0');
            }
        }elseif ($admin_type==4){
            $manager = ShopInfo::findOne(['wa_id'=>$admin_id]);
            if(!empty($manager)){
                $query->andWhere("order_info.sid=$manager->id");
            }else{
                $query->andWhere('order_info.sid=0');
            }
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $sort = $dataProvider->getSort();
        $sort->attributes = [
            'profit'=>[
                'asc' => ['profit' => SORT_ASC],
                'desc' => ['profit' => SORT_DESC],
                'label' => 'profit',
            ],
            'real_profit'=>[
                'asc' => ['real_profit' => SORT_ASC],
                'desc' => ['real_profit' => SORT_DESC],
                'label' => 'real_profit',
            ],
            'discount'=>[
                'asc' => ['discount' => SORT_ASC],
                'desc' => ['discount' => SORT_DESC],
                'label' => 'discount',
            ],
            'point'=>[
                'asc' => ['point' => SORT_ASC],
                'desc' => ['point' => SORT_DESC],
                'label' => 'point',
            ],
            'cost'=>[
                'asc' => ['cost' => SORT_ASC],
                'desc' => ['cost' => SORT_DESC],
                'label' => 'cost',
            ],
            'order_date'=>[
                'asc' => ['order_info.order_date' => SORT_ASC],
                'desc' => ['order_info.order_date' => SORT_DESC],
                'label' => 'order_info.order_date',
            ],
            'pay_bill'=>[
                'asc' => ['order_info.pay_bill' => SORT_ASC],
                'desc' => ['order_info.pay_bill' => SORT_DESC],
                'label' => 'order_info.pay_bill',
            ],
        ];
        $sort->defaultOrder = ['order_date' => SORT_DESC];
        $dataProvider->pagination->pageSize=100;
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'order_info.order_code', $this->order_code]);
        if(!empty($this->sid)){
            $query->andFilterWhere(['in','order_info.sid',$this->sid]);
        }
        if(!empty($this->good_type)){
            $query->andFilterWhere(['in','good_type.id',$this->good_type]);
        }
        $query->andFilterWhere(['=', 'order_info.pay_id', $this->pay_id])
            ->andFilterWhere(['=', 'gid', $this->gid]);
        if(!empty($this->order_date)){
            $order_date = explode('to',str_replace(' ','',$this->order_date));
            $query->andFilterWhere(['between', 'order_info.order_date', strtotime("$order_date[0] 00:00:00"),strtotime("$order_date[1] 23:59:59")]);
        }
        return $dataProvider;
    }
}
