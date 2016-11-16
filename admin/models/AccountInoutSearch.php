<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AdListSearch represents the model behind the search form about `admin\models\AdList`.
 */
class AccountInoutSearch extends AccountInout
{
    public function rules()
    {
        return [
            [['pay_id'],'integer'],
            [['phone'],'string'],
            [['pay_date'],'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = self::find()->joinWith(['inoutPay'])->leftJoin('user_info','inout_pay.uid=user_info.id')
            ->where("type=4 and sum>0 and account_inout.status=1 and inout_pay.id>0 and inout_pay.status=1");
        $query->addSelect(['account_inout.*','inout_pay.pay_id as pay_id','inout_pay.pay_date as pay_date','user_info.phone as phone']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $sort = $dataProvider->getSort();
        $sort->attributes = [
            'pay_date'=>[
                'asc' => ['pay_date' => SORT_ASC],
                'desc' => ['pay_date' => SORT_DESC],
                'label' => 'pay_date',
            ],
            'sum'=>[
                'asc' => ['sum' => SORT_ASC],
                'desc' => ['sum' => SORT_DESC],
                'label' => 'sum',
            ],
        ];
        $sort->defaultOrder = ['pay_date' => SORT_DESC];
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere(['inout_pay.pay_id'=>$this->pay_id]);
        $query->andFilterWhere(['like','user_info.phone',$this->phone]);
        if(!empty($this->pay_date)){
            $pay_date = explode('to',str_replace(' ','',$this->pay_date));
            $query->andFilterWhere(['between', 'inout_pay.pay_date', strtotime("$pay_date[0] 00:00:00"),strtotime("$pay_date[1] 23:59:59")]);
        }
        return $dataProvider;
    }
}
