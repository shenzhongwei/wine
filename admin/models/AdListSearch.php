<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\AdList;

/**
 * AdListSearch represents the model behind the search form about `admin\models\AdList`.
 */
class AdListSearch extends AdList
{
    public function rules()
    {
        return [
            [['id', 'type', 'target_id', 'is_show'], 'integer'],
            [['pic', 'pic_url'], 'safe'],

            [['target_name'],'string']
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params,$type,$postion)
    {
        $str = $type==7 ? 'type=7':'type<>7';
        $query = AdList::find()->where("$str and postion=$postion and is_show=1");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
}
