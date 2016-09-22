<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\GoodRush;

/**
 * RushSearch represents the model behind the search form about `admin\models\GoodRush`.
 */
class RushSearch extends GoodRush
{
    public $start_price;
    public $end_price;
    public $good_name;
    public function rules()
    {
        return [
            [['id', 'gid', 'limit', 'amount', 'is_active'], 'integer'],
            [['price','start_price','end_price'], 'number'],
            [['good_name'],'string'],
            [['start_at', 'end_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $admin = Yii::$app->user->identity;
        $adminType = $admin->wa_type;
        $adminId = $admin->wa_id;
        $query = GoodRush::find()->joinWith(['g' => function ($q) {
            $q->from(GoodInfo::tableName());
        }]);
//        var_dump($query->asArray()->all());
//        exit;
        if($adminType>2){
            $query->where(['good_info.merchant'=>$adminId]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $sort = $dataProvider->getSort();
        $sort->attributes['g.price'] = [
            'asc' => ['good_info.price' => SORT_ASC],
            'desc' => ['good_info.price' => SORT_DESC],
            'label' => 'good_info.price',
        ];
        $sort->attributes['g.is_active'] = [
            'asc' => ['good_info.is_active' => SORT_ASC],
            'desc' => ['good_info.is_active' => SORT_DESC],
            'label' => 'good_info.is_active',
        ];
        $sort->defaultOrder = ['is_active' => SORT_DESC,'g.is_active'=>SORT_DESC];
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'good_info.name' => $this->good_name,
            'limit' => $this->limit,
            'good_rush.is_active' => $this->is_active,
        ]);
        $query->andFilterWhere(['>=','good_rush.price',$this->price])
            ->andFilterWhere(['>=','start_at',$this->start_at])
            ->andFilterWhere(['<=','end_at',$this->end_at]);
        return $dataProvider;
    }
}
