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
            [['is_active'], 'integer'],
            [['name', 'logo','regist_at'], 'safe'],
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
//            'id' => $this->id,
//            'type' => $this->type,
            'is_active' => $this->is_active,
        ]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['>=',"FROM_UNIXTIME(regist_at,'%Y年%m月%d日')",$this->regist_at]);
        return $dataProvider;
    }
}
