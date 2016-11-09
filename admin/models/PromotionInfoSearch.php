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
        $validPro = PromotionInfo::find()->where("is_active=1 and end_at>0 and end_at<".time())->one();
        if(!empty($validPro)){
            $sql = "UPDATE promotion_info SET is_active=0 AND active_at=".time()." WHERE is_active=1 AND end_at>0 AND end_at<".time();
            $result = Yii::$app->db->createCommand($sql)->execute();
        }
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

        $query->andFilterWhere(['>=','condition',$this->condition])
            ->andFilterWhere(['>=','discount',$this->discount]);

        //活动名称
        $query->andFilterWhere(['like', 'name', $this->name]);
        if(!empty($this->start_at)){
            $query->andFilterWhere(['>=','start_at',strtotime($this->start_at.' 00:00:00')]);
        }
        if(!empty($this->end_at)){
            $query->andFilterWhere(['<=','end_at',strtotime($this->end_at.' 23:59:59')]);
        }

        return $dataProvider;
    }
}
