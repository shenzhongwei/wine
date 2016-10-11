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
            [['id', 'type','start','end'], 'integer'],
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
        $query->sort = false;
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'type' => $this->type,
        ]);

        $query->andFilterWhere("STRING_INDEX(discription,',',1),2)<")
            ->andFilterWhere(['>=','start_at',$this->start_at])
            ->andFilterWhere(['<=','end_at',$this->end_at]);

        return $dataProvider;
    }
}
