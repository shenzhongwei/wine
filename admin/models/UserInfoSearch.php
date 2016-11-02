<?php

namespace admin\models;

use common\helpers\ArrayHelper;
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
            [['end'],'number'],
            [['name','invite_user'],'string','max'=>50]
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public static function getAllCode(){
        $data = ArrayHelper::getColumn(self::findAllUser(),'invite_code');
        return $data;
    }

    public static function geAllName(){
        $data = array_values(array_unique(ArrayHelper::getColumn(self::findAllUser(),'nickname')));
        return $data;
    }

    public static function geInviter(){
        $users = UserInfo::find()->where("id in (select invite_user_id from user_info where invite_user_id>0)")->all();
        $data = ArrayHelper::map($users,'id','nickname');
        return $data;
    }

    public static function geAllPhone(){
        $data = ArrayHelper::getColumn(self::findAllUser(),'phone');
        return $data;
    }

    public static function findAllUser(){
        return UserInfo::find()->all();
    }

    public function search($params)
    {
        $query = UserInfo::find()->leftJoin(
            'user_account',
            "`user_info`.`id` = `user_account`.`target` and user_account.type=1 and user_account.level=2 and user_account.is_active=1")
            ->addSelect(['user_info.*','IFNULL(`end`,0) as end']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $sort = $dataProvider->getSort();
        $dataProvider->pagination = [
            'pageSize'=>15,
        ];
        $sort->attributes = [
            'end'=>[
                'asc' => ['end' => SORT_ASC],
                'desc' => ['end' => SORT_DESC],
                'label' => 'end',
            ],
            'is_vip'=>[
                'asc' => ['is_vip' => SORT_ASC],
                'desc' => ['is_vip' => SORT_DESC],
                'label' => 'is_vip',
            ],
            'status'=>[
                'asc' => ['status' => SORT_ASC],
                'desc' => ['status' => SORT_DESC],
                'label' => 'status',
            ],
            'created_time'=>[
                'asc' => ['created_time' => SORT_ASC],
                'desc' => ['created_time' => SORT_DESC],
                'label' => 'created_time',
            ],
        ];
        $sort ->defaultOrder = ['is_vip'=>SORT_DESC,'status'=>SORT_DESC,'created_time'=>SORT_DESC,];
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'is_vip' => $this->is_vip,
            'status' => $this->status,
            'invite_user_id' => $this->invite_user,
        ]);
        if(!empty($this->created_time)){
            $create_date = explode('to',str_replace(' ','',$this->created_time));
            $query->andFilterWhere(['between', 'created_time', "$create_date[0] 00:00:00","$create_date[1] 23:59:59"]);
        }
        $query->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'invite_code', $this->invite_code]);
        $query->andFilterWhere(['>=', 'end', $this->end]);
        return $dataProvider;
    }
}
