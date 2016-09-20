<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\MerchantInfo;
use yii\helpers\ArrayHelper;

/**
 * MerchantInfoSearch represents the model behind the search form about `admin\models\MerchantInfo`.
 */
class MerchantInfoSearch extends MerchantInfo
{

    public function rules()
    {
        return [
            [['id', 'wa_id', 'lat', 'lng', 'registe_at', 'is_active', 'active_at'], 'integer'],
            [['name', 'region', 'address', 'phone', 'province', 'city', 'district'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = MerchantInfo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'wa_id' => $this->wa_id,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'registe_at' => $this->registe_at,
            'is_active' => $this->is_active,
            'active_at' => $this->active_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'region', $this->region])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'district', $this->district]);

        return $dataProvider;
    }

    /*查询所有商户名称*/
    public static function getAllMerchant(){
        $model=MerchantInfo::find()->asArray()->all();

        $data=ArrayHelper::getColumn($model,function($element){
            return  $element['name'];
        });
        return $data;
    }

    /*查询某一商户名称*/
    public static function getOneMerchant($id){
        $model=MerchantInfo::findOne($id);
        return empty($model)?'':$model->name;
    }
}
