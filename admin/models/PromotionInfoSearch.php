<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\PromotionInfo;

/**
 * PromotionInfoSearch represents the model behind the search form about `admin\models\PromotionInfo`.
 */
class PromotionInfoSearch extends PromotionInfo
{
    public function rules()
    {
        return [
            [['id', 'pt_id', 'limit', 'target_id', 'valid_circle', 'start_at', 'end_at', 'time', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name'], 'safe'],
            [['condition', 'discount'], 'number'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = PromotionInfo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'pt_id' => $this->pt_id,
            'limit' => $this->limit,
            'target_id' => $this->target_id,
            'condition' => $this->condition,
            'discount' => $this->discount,
            'valid_circle' => $this->valid_circle,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'time' => $this->time,
            'regist_at' => $this->regist_at,
            'is_active' => $this->is_active,
            'active_at' => $this->active_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
