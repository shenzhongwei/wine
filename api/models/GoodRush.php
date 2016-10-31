<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "good_rush".
 *
 * @property integer $id
 * @property integer $gid
 * @property string $price
 * @property integer $limit
 * @property string $rush_pay
 * @property integer $point_sup
 * @property integer $amount
 * @property integer $start_at
 * @property integer $end_at
 * @property integer $is_active
 *
 * @property GoodInfo $g
 * @property OrderDetail[] $orderDetails
 */
class GoodRush extends \yii\db\ActiveRecord
{
    public $uid;
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
            [['gid', 'limit', 'amount', 'is_active','point_sup','start_at','end_at'], 'integer'],
            [['price'], 'number'],
            [['rush_pay'], 'string', 'max' => 10],
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
            'price' => '会员专享价',
            'limit' => '单次购买最大数量',
            'amount' => '抢购数量',
            'start_at' => '开始时间',
            'end_at' => '结束时间',
            'is_active' => '是否上架',
            'point_sup'=>'积分支持',
            'rush_pay'=>'抢购支付方式'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getG()
    {
        return $this->hasOne(GoodInfo::className(), ['id' => 'gid'])->where(['and','good_info.is_active=1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderDetails()
    {
        return $this->hasMany(OrderDetail::className(), ['rush_id' => 'id']);
    }
}
