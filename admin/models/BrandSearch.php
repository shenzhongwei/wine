<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\GoodBrand;

/**
 * BrandSearch represents the model behind the search form about `admin\models\GoodBrand`.
 */
class BrandSearch extends GoodBrand
{
    public function rules()
    {
        return [
            [['id', 'type', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name', 'logo'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params,$id)
    {
        $query = GoodBrand::find()->where(['type'=>$id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->defaultOrder=[
            'is_active'=>SORT_DESC,
        ];
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

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'logo', $this->logo]);

        return $dataProvider;
    }
}
