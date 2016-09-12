<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\UserInfo;

/**
 * UserInfoSearch represents the model behind the search form about `admin\models\UserInfo`.
 */
class UserInfoSearch extends UserInfo
{
    public function rules()
    {
        return [
            [['id', 'invite_user_id', 'is_vip', 'status'], 'integer'],
            [['phone', 'sex', 'head_url', 'birth', 'nickname', 'realname', 'invite_code', 'created_time', 'updated_time'], 'safe'],

            [['name','invite_user'],'string','max'=>50]
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = UserInfo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>[
                'pageSize' => 10,
            ],
            'sort' => [   //排序
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $username=$params['UserInfoSearch']['invite_user'];
        if(!empty($username)){
            $model=UserInfo::find()->select(['id'])->andWhere(['like','realname',$username])->orWhere(['like','nickname',$username])->asArray()->all();
            $userids=array();
            foreach($model as $k=>$v){
                $userids[]=$v['id'];
            }
            $query->andFilterWhere(['in','invite_user_id',$userids]);
        }

        $query->andFilterWhere([
            'is_vip' => $this->is_vip,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'nickname', $this->name])
            ->orFilterWhere(['like', 'realname', $this->name])
            ->andFilterWhere(['like', 'invite_code', $this->invite_code]);

        return $dataProvider;
    }
}
