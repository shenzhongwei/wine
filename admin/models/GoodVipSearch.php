<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\GoodVip;

/**
 * GoodVipSearch represents the model behind the search form about `admin\models\GoodVip`.
 */
class GoodVipSearch extends GoodVip
{
    public function rules()
    {
        return [
            [['id', 'gid', 'limit', 'is_active'], 'integer'],
            [['price'], 'number'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GoodVip::find()->joinWith('g');

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
            'is_active' => $this->is_active,
        ]);

        return $dataProvider;
    }
}
