<?php

namespace admin\models;

use common\helpers\ArrayHelper;
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
            [['id', 'aid', 'pay_id', 'pay_date', 'ticket_id', 'send_id', 'send_date', 'state','is_del', 'status','step','type'], 'integer'],
            [['order_date','username','order_code','pay_date'], 'safe'],
            [['total', 'disc', 'pay_bill'], 'number'],
            [['is_ticket','is_point'],'integer'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = self::Query();
        $query->joinWith('u');
        $query->addSelect(['order_info.*','(discount+(point/100)) as disc','(CASE state WHEN 1 THEN 98 ELSE state END) as step',
        'user_info.phone as username','(CASE WHEN point>0 THEN 1 ELSE 0 END) as is_point',
            '(CASE WHEN ticket_id>0 THEN 1 ELSE 0 END) as is_ticket']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $sort = $dataProvider->getSort();
        $sort->attributes['step'] = [
                'asc' => ['step' => SORT_ASC],
                'desc' => ['step' => SORT_DESC],
                'label' => 'step',
        ];
        $sort->attributes['disc'] = [
            'asc' => ['disc' => SORT_ASC],
            'desc' => ['disc' => SORT_DESC],
            'label' => 'disc',
        ];
        $sort->attributes['is_ticket'] = [
            'asc' => ['is_ticket' => SORT_ASC],
            'desc' => ['is_ticket' => SORT_DESC],
            'label' => 'is_ticket',
        ];
        $sort->attributes['is_point'] = [
            'asc' => ['is_point' => SORT_ASC],
            'desc' => ['is_point' => SORT_DESC],
            'label' => 'is_point',
        ];
        $sort->defaultOrder = ['step'=>SORT_ASC,'order_date'=>SORT_ASC];
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'type' => $this->type,
            'state' => $this->step,
            'pay_id'=>$this->pay_id,
            'order_info.status'=>$this->status,
        ]);
        $query->andFilterWhere(['like', 'user_info.phone', $this->username])
            ->andFilterWhere(['like', 'order_code', $this->order_code]);
        $query->andFilterWhere(['>=','total',$this->total])
            ->andFilterWhere(['>=','pay_bill',$this->pay_bill]);
        $query->andFilterWhere(['>=','(discount+(point/100))',$this->disc]);
        if(empty($this->is_point) && $this->is_ticket!==''){
            $query->andFilterWhere(['=', 'point', 0]);
        }elseif($this->is_ticket>0){
            $query->andFilterWhere(['>', 'point', 0]);
        }
        if(empty($this->is_ticket) && $this->is_ticket!==''){
            $query->andFilterWhere(['=', 'ticket_id', 0]);
        }elseif($this->is_ticket>0){
            $query->andFilterWhere(['>', 'ticket_id', 0]);
        }
        if(!empty($this->order_date)){
            $order_date = explode('to',str_replace(' ','',$this->order_date));
            $query->andFilterWhere(['between', 'order_info.order_date', strtotime("$order_date[0] 00:00:00"),strtotime("$order_date[1] 23:59:59")]);
        }
        if(!empty($this->pay_date)){
            $pay_date = explode('to',str_replace(' ','',$this->pay_date));
            $query->andFilterWhere(['between', 'order_info.pay_date', strtotime("$pay_date[0] 00:00:00"),strtotime("$pay_date[1] 23:59:59")]);
        }
        return $dataProvider;
    }


}
