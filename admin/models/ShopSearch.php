<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\ShopInfo;

/**
 * ShopSearch represents the model behind the search form about `admin\models\ShopInfo`.
 */
class ShopSearch extends ShopInfo
{
    public function rules()
    {
        return [
            [['id', 'wa_id', 'lat', 'lng', 'limit', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name', 'region', 'address', 'bus_pic', 'logo', 'province', 'city', 'district'], 'safe'],
            [['least_money', 'send_bill', 'no_send_need'], 'number'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = ShopInfo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $merchant=$params['ShopSearch']['merchant'];
        $model=MerchantInfo::find()->select(['id'])->andWhere(['like','name',$merchant])->asArray()->all();
        $merids=array();
        foreach($model as $k=>$v){
            $merids[]=$v['id'];
        }

        $query->andFilterWhere([
            'limit' => $this->limit,
            'least_money' => $this->least_money,
            'send_bill' => $this->send_bill,
            'no_send_need' => $this->no_send_need,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['in','merchant',$merids])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'region', $this->region])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'bus_pic', $this->bus_pic])
            ->andFilterWhere(['like', 'logo', $this->logo])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'district', $this->district]);

        return $dataProvider;
    }
}
