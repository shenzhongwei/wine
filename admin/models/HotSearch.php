<?php

namespace admin\models;

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
            [['name','order'],'required'],
            ['name','validName'],
            ['order','compare','compareValue'=>1,'operator'=>'>=','message'=>'请输入1~5之间的整数'],
            ['order','compare','compareValue'=>5,'operator'=>'<=','message'=>'请输入1~5之间的整数'],
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

    public function validName(){
        $query = self::find()->where(['name'=>$this->name]);
        if(!empty($this->id)){
            $query->andWhere('id<>'.$this->id);
        }
        $model = $query->one();
        if(!empty($model)){
            return $this->addError('name','该热搜已存在，请重新输入');
        }
    }
}
