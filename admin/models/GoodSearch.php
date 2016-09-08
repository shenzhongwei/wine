<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\GoodInfo;

/**
 * GoodSearch represents the model behind the search form about `admin\models\GoodInfo`.
 */
class GoodSearch extends GoodInfo
{

    public $start_price;
    public $end_price;

    public function rules()
    {
        return [
            [['id', 'merchant', 'type', 'brand', 'smell', 'color', 'dry', 'boot', 'breed', 'country', 'style', 'order', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name', 'volum', 'unit', 'pic', 'number', 'detail'], 'safe'],
            [['price','start_price','end_price'], 'number'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GoodInfo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'merchant' => $this->merchant,
            'type' => $this->type,
            'brand' => $this->brand,
            'smell' => $this->smell,
            'color' => $this->color,
            'dry' => $this->dry,
            'boot' => $this->boot,
            'breed' => $this->breed,
            'country' => $this->country,
            'style' => $this->style,
            'order' => $this->order,
            'regist_at' => $this->regist_at,
            'is_active' => $this->is_active,
            'active_at' => $this->active_at,
        ]);
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'volum', $this->volum])
            ->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'pic', $this->pic])
            ->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'detail', $this->detail]);
        $query->andFilterWhere(['>=','price',$this->start_price])
            ->andFilterWhere(['<=','price',$this->end_price]);
        return $dataProvider;
    }
}
