<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\OrderInfo;

/**
 * OrderInfoSearch represents the model behind the search form about `admin\models\OrderInfo`.
 */
class OrderInfoSearch extends OrderInfo
{

    public function rules()
    {
        return [
            [['id', 'aid', 'pay_id', 'pay_date', 'ticket_id', 'send_id', 'state', 'send_date', 'is_del', 'status'], 'integer'],
            [['order_code', 'send_code'], 'safe'],
            [['total', 'discount', 'send_bill', 'pay_bill'], 'number'],

            [['name','nickname'],'string','max'=>50],
            [['is_ticket'],'integer'],
            [['order_date_from','order_date_to'],'date','format'=>'yyyy-mm-dd']
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = OrderInfo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->joinWith('s')
            ->joinWith('u')
            ->andFilterWhere(['like','shop_info.name',$params['OrderInfoSearch']['name']])
            ->andFilterWhere(['like','user_info.nickname',$params['OrderInfoSearch']['nickname']])
            ->orFilterWhere(['like','user_info.realname',$params['OrderInfoSearch']['nickname']]);

        $query->andFilterWhere([ 'total' => $this->total ]);
        //下单时间
        if(!empty($params['OrderInfoSearch']['order_date_from']) && !empty($params['OrderInfoSearch']['order_date_to']) ){
            $query->andFilterWhere(['between','order_date',strtotime($params['OrderInfoSearch']['order_date_from'].' 00:00:00'),strtotime($params['OrderInfoSearch']['order_date_to'].'00:00:00')]);
        }else{
            if(!empty($params['OrderInfoSearch']['order_date_from'])){
                $query->andFilterWhere(['>=','order_date',strtotime($params['OrderInfoSearch']['order_date_from'].' 00:00:00')]);
            }else{
                $query->andFilterWhere(['<=','order_date',strtotime($params['OrderInfoSearch']['order_date_to'].' 23:59:59')]);
            }
        }
        //有无优惠券
        if($params['OrderInfoSearch']['is_ticket']=='1'){
            $query->andFilterWhere(['!=', 'is_ticket', 0]);
        }
        //支付方式
        if(!empty($params['OrderInfoSearch']['pay_id'])){
            $query->andFilterWhere(['pay_id'=>$params['OrderInfoSearch']['pay_id']]);
        }
        $query->andFilterWhere(['like', 'order_code', $this->order_code])
            ->andFilterWhere(['like', 'send_code', $this->send_code]);

        return $dataProvider;
    }

    /*
     * 获取支付方式
     */
    public static function getPaytype($pay_id=0){
        if(empty($pay_id)){
            $model=Dics::find()->select(['id','name'])->where(['type'=>'付款方式'])->asArray()->all();
            return empty($model)?[]:$model;
        }else{
            $model=Dics::find()->select(['name'])->where(['type'=>'付款方式','id'=>$pay_id])->asArray()->one();
            return empty($model)?'':$model['name'];
        }

    }

    /*
     *获取配送进度
     */
    public static function getOrderstep($state){
        $model=Dics::find()->select(['name'])->where(['type'=>'订单状态','id'=>$state])->asArray()->one();
        return empty($model)?'':$model['name'];
    }
}
