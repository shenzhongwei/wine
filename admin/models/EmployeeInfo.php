<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "employee_info".
 *
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property integer $type
 * @property integer $owner_id
 * @property integer $register_at
 * @property integer $status
 *
 * @property OrderInfo[] $orderInfos
 */
class EmployeeInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employee_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'owner_id', 'register_at', 'status'], 'integer'],
            [['name'], 'string', 'max' => 25],
            [['phone'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'type' => 'Type',
            'owner_id' => 'Owner ID',
            'register_at' => 'Register At',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfos()
    {
        return $this->hasMany(OrderInfo::className(), ['send_id' => 'id']);
    }
}
