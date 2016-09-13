<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\PromotionType;

/**
 * PromotionTypeSearch represents the model behind the search form about `admin\models\PromotionType`.
 */
class PromotionTypeSearch extends PromotionType
{
    public function rules()
    {
        return [
            [['id', 'class', 'group', 'regist_at', 'is_active', 'active_at'], 'integer'],
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
        $query = PromotionType::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'class' => $this->class,
            'group' => $this->group,
            'regist_at' => $this->regist_at,
            'is_active' => $this->is_active,
            'active_at' => $this->active_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
