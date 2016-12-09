<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserAccountSearch represents the model behind the search form about `admin\models\UserAccount`.
 */
class UserAccountSearch extends UserAccount
{
    public function rules()
    {
        return [
            [['level', 'type','is_active','set_pwd'], 'integer'],
            [['start', 'end'], 'number'],
            [['create_at','target'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = UserAccount::find();
        $query->addSelect(['user_account.*',"CASE WHEN `pay_password`='' THEN 0 ELSE 1 END AS `set_pwd`"]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $sort = $dataProvider->getSort();
        $sort->attributes = [
            'end'=>[
                'asc'=>['end'=>SORT_ASC],
                'desc'=>['end'=>SORT_DESC],
                'attribute'=>'end'
            ],
            'set_pwd'=>[
                'asc'=>['set_pwd'=>SORT_ASC],
                'desc'=>['set_pwd'=>SORT_DESC],
                'attribute'=>'set_pwd'
            ],
            'create_at'=>[
                'asc'=>['create_at'=>SORT_ASC],
                'desc'=>['create_at'=>SORT_DESC],
                'attribute'=>'create_at'
            ],
            'is_active'=>[
                'asc'=>['is_active'=>SORT_ASC],
                'desc'=>['is_active'=>SORT_DESC],
                'attribute'=>'is_active'
            ],
            'update_at'=>[
                'asc'=>['update_at'=>SORT_ASC],
                'desc'=>['update_at'=>SORT_DESC],
                'attribute'=>'update_at'
            ],
            'level'=>[
                'asc'=>['level'=>SORT_ASC],
                'desc'=>['level'=>SORT_DESC],
                'attribute'=>'level'
            ],
        ];
        $sort->defaultOrder = [
            'update_at'=>SORT_DESC,'level'=>SORT_DESC
        ];
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'level' => $this->level,
            'type' => $this->type,
            'is_active'=>$this->is_active,
        ]);
        $query->andFilterWhere(['>=','end',$this->end])
            ->andFilterWhere(['>=',"FROM_UNIXTIME(create_at,'%Y年%m月%d日')",$this->create_at]);
        if($this->set_pwd==='0'){
            $query->andWhere('pay_password=""');
        }elseif ($this->set_pwd==='1'){
            $query->andWhere('pay_password!=""');
        }
        return $dataProvider;
    }
}
