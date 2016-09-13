<?php

namespace admin\models;

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
            [['pay_password'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'target' => 'Target',
            'level' => 'Level',
            'type' => 'Type',
            'start' => 'Start',
            'end' => 'End',
            'pay_password' => 'Pay Password',
            'create_at' => 'Create At',
            'is_active' => 'Is Active',
            'update_at' => 'Update At',
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
