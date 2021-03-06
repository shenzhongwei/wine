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
            [['id', 'gid', 'limit', 'amount', 'is_active','point_sup'], 'integer'],
            [['price','start_price','end_price'], 'number'],
            [['good_name'],'string'],
            [['start_at', 'end_at','rush_pay'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $validRush = GoodRush::find()->where("is_active=1 and end_at>0 and end_at<".time())->one();
        if(!empty($validRush)){
            $sql = "UPDATE good_rush SET is_active=0  WHERE is_active=1 AND end_at>0 AND end_at<".time();
            $result = Yii::$app->db->createCommand($sql)->execute();
        }
        $admin = Yii::$app->user->identity;
        $adminType = $admin->wa_type;
        $adminId = $admin->wa_id;
        $query = GoodRush::find()->joinWith(['g' => function ($q) {
            $q->from(GoodInfo::tableName());
        }]);
//        var_dump($query->asArray()->all());
//        exit;
        if($adminType>2){
            $manager = MerchantInfo::findOne(['wa_id'=>$adminId]);
            $query->andWhere(['good_info.merchant'=>empty($manager) ? 0:$manager->id]);
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
            'limit' => $this->limit,
            'good_rush.is_active' => $this->is_active,
            'good_rush.point_sup'=>$this->point_sup,
        ]);
//        var_dump($this->good_name);
//        exit;
        $query->andFilterWhere(['like','good_rush.rush_pay',$this->rush_pay])
            ->andFilterWhere(['like','good_info.name',$this->good_name]);
        $query->andFilterWhere(['>=','good_rush.price',$this->price]);
        if(!empty($this->start_at)){
            $query->andFilterWhere(['>=','start_at',strtotime($this->start_at.' 00:00:00')]);
        }
        if(!empty($this->end_at)){
            $query->andFilterWhere(['<=','end_at',strtotime($this->end_at.' 23:59:59')]);
        }
        return $dataProvider;
    }
}
