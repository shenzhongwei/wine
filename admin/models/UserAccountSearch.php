<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\UserAccount;

/**
 * UserAccountSearch represents the model behind the search form about `admin\models\UserAccount`.
 */
class UserAccountSearch extends UserAccount
{
    public function rules()
    {
        return [
            [['id', 'target', 'level', 'type', 'create_at', 'is_active', 'update_at'], 'integer'],
            [['start', 'end'], 'number'],
            [['pay_password'], 'safe'],

            [['target_name'],'string'],
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

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $account=$params['UserAccountSearch'];

        if(!empty($account['target_name'])){
            $query->joinWith('u')
                ->andFilterWhere(['like', 'user_info.realname', $account['target_name']]);

        }
        $query->andFilterWhere([
            'level' => $this->level,
            'type' => $this->type,
            'start' => $this->start,
            'end' => $this->end,
        ]);

        return $dataProvider;
    }
}
