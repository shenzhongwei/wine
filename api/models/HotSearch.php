<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "hot_search".
 *
 * @property string $name
 * @property integer $id
 * @property integer $order
 */
class HotSearch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hot_search';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => '热搜名',
            'id' => 'ID',
            'order' => '热搜顺序',
        ];
    }
}
