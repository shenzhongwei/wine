<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "user_account".
 *
 * @property integer $id
 * @property integer $target
 * @property integer $level
 * @property integer $type
 * @property string $start
 * @property string $end
 * @property string $pay_password
 * @property integer $create_at
 * @property integer $is_active
 * @property integer $update_at
 *
 * @property AccountInout[] $accountInouts
 */
class UserAccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target', 'level', 'type', 'create_at', 'is_active', 'update_at'], 'integer'],
            [['start', 'end'], 'number'],
            [['pay_password'],'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'target' => '对象',
            'level' => '钱包级别，1管理员 2用户',
            'type' => '钱包类型 1余额 2支付宝 3微信',
            'start' => '开始金额',
            'end' => '最终金额',
            'pay_password'=>'余额支付密码',
            'create_at' => '开通时间',
            'is_active' => '是否激活',
            'update_at' => '状态更改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountInouts()
    {
        return $this->hasMany(AccountInout::className(), ['aid' => 'id']);
    }
}
