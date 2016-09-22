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
            [['id', 'merchant', 'type', 'brand', 'smell', 'color', 'dry', 'boot', 'breed', 'country', 'style', 'order', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name', 'volum', 'unit', 'pic', 'number', 'detail'], 'safe'],
            [['price','start_price','end_price'], 'number'],
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
        var_dump($adminId);
        $query = GoodInfo::find();
        if($adminType>2){
            $query->where(['merchant'=>$adminId]);
        }
        var_dump($query->asArray()->all());
        exit;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'merchant' => $this->merchant,
            'type' => $this->type,
            'is_active' => $this->is_active,
        ]);
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'volum', $this->volum])
            ->andFilterWhere(['like', 'number', $this->number]);
        $query->andFilterWhere(['>=','price',$this->start_price])
            ->andFilterWhere(['<=','price',$this->end_price]);
        return $dataProvider;
    }
}
