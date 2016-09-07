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
            [['id', 'sid', 'uid', 'aid', 'order_date', 'pay_id', 'pay_date', 'ticket_id', 'send_id', 'state', 'send_date', 'is_del', 'status'], 'integer'],
            [['order_code', 'send_code'], 'safe'],
            [['total', 'discount', 'send_bill', 'pay_bill'], 'number'],
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

        $query->andFilterWhere([
            'id' => $this->id,
            'sid' => $this->sid,
            'uid' => $this->uid,
            'aid' => $this->aid,
            'order_date' => $this->order_date,
            'pay_id' => $this->pay_id,
            'pay_date' => $this->pay_date,
            'total' => $this->total,
            'discount' => $this->discount,
            'ticket_id' => $this->ticket_id,
            'send_bill' => $this->send_bill,
            'send_id' => $this->send_id,
            'pay_bill' => $this->pay_bill,
            'state' => $this->state,
            'send_date' => $this->send_date,
            'is_del' => $this->is_del,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'order_code', $this->order_code])
            ->andFilterWhere(['like', 'send_code', $this->send_code]);

        return $dataProvider;
    }
}
