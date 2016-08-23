<?php

namespace api\models;

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
            'id' => '主键',
            'name' => '姓名',
            'phone' => '手机号',
            'type' => '类型，0商家配送员，1店铺配送员',
            'owner_id' => '上级id',
            'register_at' => '登记时间',
            'status' => '状态，0删除，1正常，2繁忙，3下岗',
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
