<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\AdList;

/**
 * AdListSearch represents the model behind the search form about `admin\models\AdList`.
 */
class AdListSearch extends AdList
{
    public function rules()
    {
        return [
            [['id', 'type', 'target_id', 'is_show'], 'integer'],
            [['pic', 'url'], 'safe'],

            [['target_name'],'string']
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = AdList::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $admodel=$params['AdListSearch'];
        if(!empty($admodel['target_name'])){
            $query->andFilterWhere(['target_id'=>$admodel['target_name']]);
        }
        $query->andFilterWhere([
            'type' => $this->type,
            'is_show' => $this->is_show,
        ]);

        return $dataProvider;
    }
}