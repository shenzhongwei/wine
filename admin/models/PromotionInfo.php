<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "promotion_info".
 *
 * @property integer $id
 * @property integer $pt_id
 * @property integer $limit
 * @property integer $target_id
 * @property string $name
 * @property string $condition
 * @property string $discount
 * @property integer $valid_circle
 * @property integer $start_at
 * @property integer $end_at
 * @property integer $time
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $active_at
 *
 * @property PromotionType $pt
 * @property UserTicket[] $userTickets
 */
class PromotionInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promotion_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pt_id', 'limit', 'target_id', 'valid_circle', 'start_at', 'end_at', 'time', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['condition', 'discount'], 'number'],
            [['name'], 'string', 'max' => 128],
            [['pt_id'], 'exist', 'skipOnError' => true, 'targetClass' => PromotionType::className(), 'targetAttribute' => ['pt_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pt_id' => 'Pt ID',
            'limit' => 'Limit',
            'target_id' => 'Target ID',
            'name' => 'Name',
            'condition' => 'Condition',
            'discount' => 'Discount',
            'valid_circle' => 'Valid Circle',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'time' => 'Time',
            'regist_at' => 'Regist At',
            'is_active' => 'Is Active',
            'active_at' => 'Active At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPt()
    {
        return $this->hasOne(PromotionType::className(), ['id' => 'pt_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTickets()
    {
        return $this->hasMany(UserTicket::className(), ['pid' => 'id']);
    }
}
