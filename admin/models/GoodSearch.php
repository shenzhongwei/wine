<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\GoodInfo;

/**
 * GoodSearch represents the model behind the search form about `admin\models\GoodInfo`.
 */
class GoodSearch extends GoodInfo
{

    public $start_price;
    public $end_price;

    public function rules()
    {
        return [
            [[ 'merchant', 'type', 'vip_show','brand', 'point_sup','smell', 'color', 'dry','boot', 'breed', 'country', 'style', 'order', 'is_active', 'active_at'], 'integer'],
            [['name', 'volum', 'unit', 'number', 'detail', 'regist_at'], 'safe'],
            [['price','start_price','end_price','pro_price','vip_price'], 'number'],
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
        $query = GoodInfo::find();
        if($adminType>2){
            $manager = MerchantInfo::findOne(['wa_id'=>$adminId]);
            $query->where(['merchant'=>empty($manager) ? 0:$manager->id]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'merchant' => $this->merchant,
            'brand' => $this->brand,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'vip_show'=>$this->vip_show,
            'point_sup'=>$this->point_sup,
        ]);
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'volum', $this->volum])
            ->andFilterWhere(['like', 'number', $this->number]);
        $query->andFilterWhere(['>=','price',$this->price])
            ->andFilterWhere(['>=','pro_price',$this->pro_price])
            ->andFilterWhere(['>=','vip_price',$this->vip_price])
            ->andFilterWhere(['>=',"FROM_UNIXTIME(regist_at,'%Y年%m月%d日')",$this->regist_at]);
        return $dataProvider;
    }
}
