<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "order_detail".
 *
 * @property integer $id
 * @property integer $oid
 * @property integer $gid
 * @property integer $amount
 * @property integer $rush_id
 * @property string $single_price
 * @property string $total_price
 *
 * @property OrderInfo $o
 * @property GoodInfo $g
 * @property GoodRush $r
 */
class OrderDetail extends \yii\db\ActiveRecord
{
    public $order_date;
    public $order_code;
    public $sid;
    public $pay_id;
    public $pay_bill;
    public $good_type;
    public $type_name;
    public $good_name;
    public $cost;
    public $profit;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oid', 'gid', 'amount'], 'integer'],
            [['single_price', 'total_price'], 'number'],
            [['oid'], 'exist', 'skipOnError' => true, 'targetClass' => OrderInfo::className(), 'targetAttribute' => ['oid' => 'id']],
            [['gid'], 'exist', 'skipOnError' => true, 'targetClass' => GoodInfo::className(), 'targetAttribute' => ['gid' => 'id']],
            [['rush_id'], 'exist', 'skipOnError' => true, 'targetClass' => GoodRush::className(), 'targetAttribute' => ['rush_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'oid' => '订单id',
            'gid' => '产品',
            'amount' => '数量',
            'single_price' => '单价',
            'total_price' => '总价',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getO()
    {
        return $this->hasOne(OrderInfo::className(), ['id' => 'oid']);
    }

    public function getS()
    {
        return $this->hasOne(ShopInfo::className(), ['id' => 'sid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getG()
    {
        return $this->hasOne(GoodInfo::className(), ['id' => 'gid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getR()
    {
        return $this->hasOne(GoodRush::className(), ['id' => 'rush_id'])->where('good_rush.id>0');
    }


}
