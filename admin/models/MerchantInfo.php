<?php

namespace admin\models;

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "merchant_info".
 *
 * @property integer $id
 * @property string $name
 * @property integer $wa_id
 * @property string $region
 * @property string $address
 * @property integer $lat
 * @property integer $lng
 * @property integer $registe_at
 * @property integer $is_active
 * @property integer $active_at
 * @property string $province
 * @property string $phone
 * @property string $city
 * @property string $district
 *
 * @property GoodInfo[] $goodInfos
 * @property Admin $wa
 * @property ShopInfo[] $shopInfos
 */
class MerchantInfo extends \yii\db\ActiveRecord
{

    public $wa_username;
    public $wa_password;
    public $wa_type;
    public $wa_logo;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'merchant_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wa_id', 'lat', 'lng', 'registe_at', 'is_active', 'active_at'], 'integer'],
            [['name', 'address', 'province', 'city', 'district'], 'string', 'max' => 128],
            [['phone'], 'string', 'max' => 11],
            [['region'], 'string', 'max' => 50],
            [['wa_id'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::className(), 'targetAttribute' => ['wa_id' => 'wa_id']],
            [['phone','name'],'required'],
            [['wa_username'],'validusername'],
            ['phone','match','pattern'=>'/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9][0-9]{8}|17[0-9]{9}$|14[0-9]{9}$/','message'=>'手机号格式不正确'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'name' => '商户名称',
            'wa_id' => '后台管理员id',
            'region' => '所在地区',
            'address' => '详细地址',
            'lat' => '纬度',
            'phone'=>'联系方式',
            'lng' => '经度',
            'registe_at' => '入驻时间',
            'is_active' => '是否激活',
            'active_at' => '激活状态更改时间',
            'province' => '省',
            'city' => '市',
            'district' => '区',

            'wa_username'=>'后台登陆名',
            'wa_password'=>'后台登陆密码',
            'wa_type'=>'用户组类型',
            'wa_logo'=>'用户头像'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodInfos()
    {
        return $this->hasMany(GoodInfo::className(), ['merchant' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWa()
    {
        return $this->hasOne(Admin::className(), ['wa_id' => 'wa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopInfos()
    {
        return $this->hasMany(ShopInfo::className(), ['merchant' => 'id']);
    }

    /**
     * @inheritdoc
     * @return MerchantInfoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MerchantInfoQuery(get_called_class());
    }

    //判断商户后台用户名是否唯一
    public function validusername(){
        $model=Admin::findIdentityByUsername($this->wa_username);
        if(!empty($model)){
            return $this->addError('wa_username','用户名已存在');
        }
    }

    public static function GetMerchants(){
        $merchants = self::findAll(['is_active'=>1]);
        return ArrayHelper::map($merchants,'id','name');
    }
}
