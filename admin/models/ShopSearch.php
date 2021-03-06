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
            [['id', 'limit', 'is_active', 'active_at','merchant','wa_status'], 'integer'],
            [['name','region', 'city', 'district','contacter','phone'], 'string'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = ShopInfo::find()->joinWith(['wa'])->addSelect(['shop_info.*','IFNULL(wine_admin.wa_lock,1) as wa_status']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $sort = $dataProvider->getSort();
        $sort->attributes['wa_status'] = [
            'asc' => ['wa_status' => SORT_ASC],
            'desc' => ['wa_status' => SORT_DESC],
            'label' => 'wa_status',
        ];
        $sort->defaultOrder=[
            'is_active'=>SORT_DESC,
            'wa_status'=>SORT_ASC,
        ];
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'is_active' => $this->is_active,
            'merchant'=>$this->merchant,
            'city'=>$this->city,
            'district'=>$this->district,
            'region'=>$this->region,
        ]);
        if($this->wa_status==1){
            $query->andWhere('wine_admin.wa_lock=1 or wine_admin.wa_lock is null');
        }
        if($this->wa_status==='0'){
            $query->andFilterWhere(['wine_admin.wa_lock'=>$this->wa_status]);
        }
        $query->andFilterWhere(['>=', 'limit', $this->limit]);
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->name])
            ->andFilterWhere(['like', 'contacter', $this->name]);


        return $dataProvider;
    }

    public static function getOneShopname($id){
        return ShopInfo::findOne($id)->name;
    }

}
