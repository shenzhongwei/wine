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
    public $set_pwd;
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
            default:$str='<span class="no-set">未设置</span>';break;
        }
        return $str;
    }

    public static function GetLevels(){
        return [
            1=>'管理员',2=>'用 户'
        ];
    }

    public static function GetTypes(){
        return [
            1=>'余额账户',2=>'支付宝账户',3=>'微信账户'
        ];
    }

    public static function getTargets(){
        $targets = self::find()->leftJoin('wine_admin','user_account.target=wine_admin.wa_id and user_account.level=1')
            ->leftJoin('user_info','user_account.target=user_info.id and user_account.level=2')
            ->addSelect(['wine_admin.wa_name','user_info.phone'])->asArray()->all();
        $names = array_filter(array_values(array_column($targets,'wa_name')));
        $phones = array_filter(array_values(array_column($targets,'phone')));
        return array_values(array_unique(array_merge($names,$phones)));
    }
}
