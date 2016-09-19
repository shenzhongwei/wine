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
            [['id', 'pt_id', 'limit', 'target_id', 'valid_circle', 'start_at', 'end_at', 'time', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name'], 'safe'],
            [['condition', 'discount'], 'number'],

            [['start_from','end_to'],'date','format'=>'yyyy-mm-dd'],
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
        $promotion=$params['PromotionInfoSearch'];


        $query->andFilterWhere([
            'limit' => $this->limit, //活动范围
            'condition' => $this->condition, //满xx元
            'valid_circle' => $this->valid_circle, //有效期
            'is_active' => $this->is_active, //是否上架
        ]);


        //活动优惠时间
       if(!empty($promotion['start_from'])){
            $query->andFilterWhere(['>=','start_at',strtotime($promotion['start_from'].' 00:00:00')]);
       }
       if(!empty($promotion['end_to'])){
            $query->andFilterWhere(['<=','end_at',strtotime($promotion['end_to'].' 23:59:59')]);
       }

        //活动名称
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
