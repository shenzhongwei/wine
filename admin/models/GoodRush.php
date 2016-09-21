<?php

namespace admin\models;

use common\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "good_rush".
 *
 * @property integer $id
 * @property integer $gid
 * @property string $price
 * @property integer $limit
 * @property integer $amount
 * @property string $start_at
 * @property string $end_at
 * @property integer $is_active
 *
 * @property GoodInfo $g
 */
class GoodRush extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_rush';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gid'], 'required'],
            [['gid', 'limit', 'amount', 'is_active'], 'integer'],
            [['price'], 'number'],
            [['start_at', 'end_at'], 'safe'],
            [['gid'], 'exist', 'skipOnError' => true, 'targetClass' => GoodInfo::className(), 'targetAttribute' => ['gid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'gid' => '商品id',
            'price' => '抢购价',
            'limit' => '单次限购数量',
            'amount' => '抢购数量',
            'start_at' => '开始时间',
            'end_at' => '结束时间',
            'is_active' => '是否上架',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getG()
    {
        return $this->hasOne(GoodInfo::className(), ['id' => 'gid'])->where('good_info.id>0');
    }

    public static function GetGoodNames(){
        $goods = GoodInfo::find()->joinWith('goodRushes')->where('good_rush.id>0')->all();
        if(empty($goods)){
            return [];
        }
        return array_unique(ArrayHelper::getColumn($goods,'name'));
    }

}
