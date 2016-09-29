<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\GoodType;

/**
 * TypeSearch represents the model behind the search form about `admin\models\GoodType`.
 */
class TypeSearch extends GoodType
{
    public function rules()
    {
        return [
            [['id', 'is_active', 'active_at'], 'integer'],
            [['name', 'logo','regist_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GoodType::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);
        $query->andFilterWhere(['>=',"FROM_UNIXTIME(regist_at,'%Y年%m月%d日')",$this->regist_at]);
        return $dataProvider;
    }
}
