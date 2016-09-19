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
    public $target_name;
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
            'target' => '对象id',
            'level' => '钱包级别',
            'type' => '钱包类别',
            'start' => '开始金额',
            'end' => '最终金额',
            'pay_password' => 'Pay Password',
            'create_at' => 'Create At',
            'is_active' => 'Is Active',
            'update_at' => 'Update At',

            'target_name' => '对象名称',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountInouts()
    {
        return $this->hasMany(AccountInout::className(), ['aid' => 'id']);
    }

    public function getU()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'target']);
    }


    //获取用户账户的对象名称
    public static function getAccountAcceptName($model){
        switch($model->level){
            case 1: //管理员
                $str=\admin\models\Admin::findIdentity($model->target)->wa_name;
                break;
            case 2: //用户
                $str=\admin\models\UserInfo::find()->where(['id'=>$model->target])->one()->realname;
                break;
            default:$str='无';break;
        }
        return $str;
    }
}
