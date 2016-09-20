<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\MessageList;

/**
 * MessageSearch represents the model behind the search form about `admin\models\MessageList`.
 */
class MessageSearch extends MessageList
{
    public function rules()
    {
        return [
            [['id', 'type_id', 'own_id', 'target', 'status'], 'integer'],
            [['title', 'content', 'publish_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = MessageList::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'type_id' => $this->type_id,
            'own_id' => $this->own_id,
            'target' => $this->target,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
