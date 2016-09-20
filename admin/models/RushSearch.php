<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\GoodRush;

/**
 * RushSearch represents the model behind the search form about `admin\models\GoodRush`.
 */
class RushSearch extends GoodRush
{
    public function rules()
    {
        return [
            [['id', 'gid', 'limit', 'amount', 'is_active'], 'integer'],
            [['price'], 'number'],
            [['start_at', 'end_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GoodRush::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'gid' => $this->gid,
            'price' => $this->price,
            'limit' => $this->limit,
            'amount' => $this->amount,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'is_active' => $this->is_active,
        ]);

        return $dataProvider;
    }
}
