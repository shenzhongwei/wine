<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\ShopInfo;

/**
 * ShopSearch represents the model behind the search form about `admin\models\ShopInfo`.
 */
class ShopSearch extends ShopInfo
{
    public function rules()
    {
        return [
            [['id', 'wa_id', 'merchant', 'lat', 'lng', 'limit', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name', 'region', 'address', 'bus_pic', 'logo', 'province', 'city', 'district'], 'safe'],
            [['least_money', 'send_bill', 'no_send_need'], 'number'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = ShopInfo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'wa_id' => $this->wa_id,
            'merchant' => $this->merchant,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'limit' => $this->limit,
            'least_money' => $this->least_money,
            'send_bill' => $this->send_bill,
            'no_send_need' => $this->no_send_need,
            'regist_at' => $this->regist_at,
            'is_active' => $this->is_active,
            'active_at' => $this->active_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'region', $this->region])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'bus_pic', $this->bus_pic])
            ->andFilterWhere(['like', 'logo', $this->logo])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'district', $this->district]);

        return $dataProvider;
    }
}
