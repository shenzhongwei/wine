<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\GoodBreed;

/**
 * BreedSearch represents the model behind the search form about `admin\models\GoodBreed`.
 */
class BreedSearch extends GoodBreed
{
    public function rules()
    {
        return [
            [['id', 'is_active'], 'integer'],
            [['name', 'regist_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params, $id)
    {
        $query = GoodBreed::find()->where(['type' => $id])->orderBy(['is_active'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort = false;
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'is_active' => $this->is_active,
        ]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['>=', "FROM_UNIXTIME(regist_at,'%Y年%m月%d日')", $this->regist_at]);
        return $dataProvider;
    }
}
