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

            [['merchant_name'],'string','max'=>50]
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

        $merchantname=$params['ShopSearch']['merchant_name'];
        if(!empty($merchantname)){
            $model=MerchantInfo::find()->select(['id'])->andWhere(['like','name',$merchantname])->asArray()->all();
            $merids=array();
            foreach($model as $k=>$v){
                $merids[]=$v['id'];
            }
            $query->andFilterWhere(['in','merchant',$merids]);
        }

        $query->andFilterWhere([
            'limit' => $this->limit,
            'least_money' => $this->least_money,
            'send_bill' => $this->send_bill,
            'no_send_need' => $this->no_send_need,
            'is_active' => $this->is_active,
        ]);

        if(!empty($params['ShopSearch']['province'])){
            $model=Zone::find()->where(['id'=>$params['ShopSearch']['province']])->one();
            $query->andFilterWhere(['like', 'province', $model->name]);
        }
        if(!empty($params['ShopSearch']['city'])){
            $model=Zone::find()->where(['id'=>$params['ShopSearch']['city']])->one();
            $query->andFilterWhere(['like', 'city', $model->name]);
        }
        if(!empty($params['ShopSearch']['district'])){
            $model=Zone::find()->where(['id'=>$params['ShopSearch']['district']])->one();
            $query->andFilterWhere(['like', 'district', $model->name]);
        }
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'region', $this->region])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'bus_pic', $this->bus_pic])
            ->andFilterWhere(['like', 'logo', $this->logo]);


        return $dataProvider;
    }

    //根据门店id查找门店名称
    public static function getOneShopname($shop_id){
        $model=ShopInfo::findOne($shop_id);
        return empty($model)?'':$model->name;
    }
}
