<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\PromotionInfo;

/**
 * PromotionInfoSearch represents the model behind the search form about `admin\models\PromotionInfo`.
 */
class PromotionInfoSearch extends PromotionInfo
{
    public function rules()
    {
        return [
            [['id', 'pt_id', 'limit', 'target_id', 'is_active', 'active_at','style'], 'integer'],
            [['name','start_at','end_at','regist_at'], 'safe'],
            [['condition', 'discount'], 'number'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = PromotionInfo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            'limit' => $this->limit, //活动范围
            'type' => $this->pt_id,
            'target_id'=>$this->target_id,
            'style'=>$this->style,
            'is_active' => $this->is_active, //是否上架
        ]);


        //活动优惠时间

        $query->andFilterWhere(['>=','start_at',strtotime($this->start_at.' 00:00:00')])
            ->andFilterWhere(['>=','condition',$this->condition])
            ->andFilterWhere(['>=','discount',$this->discount]);
        $query->andFilterWhere(['<=','end_at',strtotime($this->end_at.' 23:59:59')]);

        //活动名称
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
