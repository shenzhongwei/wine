<?php

namespace admin\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%admin}}".
 *
 * @property integer $wa_id
 * @property string $wa_username
 * @property string $wa_password
 * @property integer $wa_type
 * @property integer $target_id
 * @property string $wa_name
 * @property string $wa_token
 * @property string $wa_phone
 * @property string $wa_logo
 * @property string $wa_last_login_time
 * @property string $wa_last_login_ip
 * @property integer $wa_lock
 * @property integer $wa_status
 * @property string $created_time
 * @property string $updated_time
 *
 * @property AuthAssignment $admingroup
 */
class Admin extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;


    public $confirm_password;
    public $item_name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wine_admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wa_username', 'wa_password'], 'required',],
            [['wa_type', 'wa_status','wa_lock','target_id'], 'integer'],
            [['wa_last_login_time', 'created_time', 'updated_time'], 'safe'],
            [['wa_username', 'wa_name','wa_phone'], 'string', 'max' => 16],
            [['wa_token','wa_password','confirm_password'], 'string', 'max' => 64],
            [['wa_logo'],'string','max'=>225],
            [['wa_last_login_ip'], 'string', 'max' => 15],
            ['wa_status', 'default', 'value' => self::STATUS_ACTIVE],
            ['wa_status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'wa_id' => '后台管理员表',
            'wa_username' => '登录名',
            'wa_phone' => '手机号',
            'wa_password' => '登录密码',
            'wa_type' => '管理员类型',
            'confirm_password'=>'确认密码',
            'wa_name' => '姓名',
            'wa_token' => '用户token',
            'wa_last_login_time' => '最近登录时间',
            'target_id'=>'对象id',
            'wa_logo' => '头像',
            'wa_last_login_ip' => '最近登录ip',
            'wa_lock' => '锁定状态',
            'wa_status' =>  '状态',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }



    //获取所有用户
    public function get_all_user(){
        $user = Yii::$app->db->createCommand('select * from wa_admin')->queryAll();
        return $user;
    }
    public function getAdmingrouplist()
    {
        /**
         * 第一个参数为要关联的字表模型类名称，
         *第二个参数指定 通过子表的 customer_id 去关联主表的 id 字段
         */
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'wa_id']);
    }

    public function getAdmingroup()
    {
        /**
         * 第一个参数为要关联的字表模型类名称，
         *第二个参数指定 通过子表的 customer_id 去关联主表的 id 字段
         */
        return $this->hasOne(AuthAssignment::className(), ['user_id' => 'wa_id']);
    }


    /**
     * @param int|string $id
     * @return mixed
     */
    public static function findIdentity($id)
    {
        return static::find()->where(['wa_id'=>$id])->one();
    }

    /**
     * @param mixed $token
     * @param mixed|null|null $type
     * @return mixed
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['wa_token' => $token]);
    }

    public static function findIdentityByUsername($username)
    {
        return static::findOne(['wa_username'=>$username]);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
       return $this->wa_id;
    }

    /**
     * @return mixed
     */
    public function getAuthKey()
    {
        return $this->wa_token;
    }

    /**
     * @param string $authKey
     * @return mixed
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function UpdateModel($admin){

    }

    public function CreateModel($admin){

    }


    //管理员type值对应的管理员类型
    public static function getadminValue($wa_type){
        switch($wa_type){
            case 1: $v='开发者'; break;
            case 2: $v='系统管理员'; break;
            case 3: $v='商家管理员'; break;
            case 4: $v='门店管理员'; break;
            default: $v='无'; break;
        }
        return $v;
    }
}
