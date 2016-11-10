<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PromotionTypeSearch represents the model behind the search form about `admin\models\PromotionType`.
 */
class PromotionTypeSearch extends PromotionType
{
    public function rules()
    {
        return [
            [[ 'limit','class', 'group', 'is_active','env'], 'integer'],
            [['name','regist_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = PromotionType::find()->where("class<>2");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'class' => $this->class,
            'group' => $this->group,
            'env' => $this->env,
            'is_active' => $this->is_active,
            'limit'=>$this->limit,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['>=',"FROM_UNIXTIME(regist_at,'%Y年%m月%d日')",$this->regist_at]);
        return $dataProvider;
    }
}
