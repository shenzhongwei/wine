<?php

namespace api\models;

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
            'id' => '主键',
            'pt_id' => '优惠类型',
            'limit' => '适用范围',
            'target_id' => '类型对应的id',
            'name' => '活动名称',
            'condition' => '条件',
            'discount' => '优惠',
            'valid_circle' => '有效期限 0表示永久有效',
            'start_at' => '开始时间',
            'end_at' => '结束时间',
            'time' => '使用次数 0表示无限制',
            'regist_at' => '添加时间',
            'is_active' => '是否上架',
            'active_at' => '上架状态更改时间',
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
