<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\GoodPriceField;

/**
 * PriceSearch represents the model behind the search form about `admin\models\GoodPriceField`.
 */
class PriceSearch extends GoodPriceField
{
    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['discription'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GoodPriceField::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'discription', $this->discription]);

        return $dataProvider;
    }
}
