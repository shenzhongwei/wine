<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\GoodCountry;

/**
 * CountrySearch represents the model behind the search form about `admin\models\GoodCountry`.
 */
class CountrySearch extends GoodCountry
{
    public function rules()
    {
        return [
            [['id', 'type', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GoodCountry::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'regist_at' => $this->regist_at,
            'is_active' => $this->is_active,
            'active_at' => $this->active_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
