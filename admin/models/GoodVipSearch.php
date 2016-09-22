<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\GoodVip;

/**
 * GoodVipSearch represents the model behind the search form about `admin\models\GoodVip`.
 */
class GoodVipSearch extends GoodVip
{
    public $start_price;
    public $end_price;
    public $good_name;

    public function rules()
    {
        return [
            [['id', 'gid', 'limit', 'is_active'], 'integer'],
            [['price','start_price','end_price'], 'number'],
            [['good_name'],'string'],
            [['gid','is_active','price','good_name','start_price','end_price'],'safe']
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
        $query = GoodVip::find()->joinWith(['g' => function ($q) {
            $q->from(GoodInfo::tableName());
        }]);
        if($adminType>2){
            $query->where(['good_info.merchant'=>$adminId]);
        }
//        var_dump($query->asArray()->all());
//        exit;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $sort = $dataProvider->getSort();
        $sort->attributes['g.price'] = [
            'asc' => ['good_info.price' => SORT_ASC],
            'desc' => ['good_info.price' => SORT_DESC],
            'label' => 'good_info.price',
        ];
        $sort->defaultOrder = ['is_active' => SORT_DESC];
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'gid' => $this->gid,
            'good_vip.is_active' => $this->is_active,
        ]);
        $query->andFilterWhere(['like', 'good_info.name', $this->good_name]);
        $query->andFilterWhere(['>=','good_vip.price',$this->start_price])
            ->andFilterWhere(['<=','good_vip.price',$this->end_price]);
        return $dataProvider;
    }
}
