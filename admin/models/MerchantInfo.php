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
            [['wa_username','wa_password'],'required','on'=>'create'],
            [['wa_username'],'validusername','on'=>'create'],
            ['phone','match','pattern'=>'/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9][0-9]{8}|17[0-9]{9}$|14[0-9]{9}$/','message'=>'手机号格式不正确'],
            ['wa_password','match','pattern'=>'/^[\w\W]{5,16}$/','message'=>'密码长度为5~16位'],
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

    //指定“新增” 模块需要验证参数的规则
    public function scenarios()
    {
        $n=parent::scenarios();
        $n['create']=['id','name','phone','province','city','district','region','address','wa_username','wa_password','wa_type','wa_logo','id'];
        return $n;
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


    //判断商户后台用户名是否唯一
    public function validusername(){
        $query = Admin::find()->where(['wa_username'=>$this->wa_username,'wa_type'=>3]);
        if(!empty($this->id)){
            $model = self::findOne($this->id);
            if(!empty($model)&&!empty($model->wa_id)){
                $query->andWhere("wa_id <> $model->wa_id");
            }
            if(!empty($model)){
                $query->andWhere("target_id<>$model->id");
            }
        }
        $model=$query->one();
        if(!empty($model)){
            $this->addError('wa_username','该后台登录名已存在');
        }
    }


    public static function GetMerchants(){
        $admin = Yii::$app->user->identity;
        $adminId = $admin->wa_id;
        $adminType = $admin->wa_type;
        $query =  self::find()->where(['is_active'=>1]);
        if($adminType>2){
            $query->andWhere(['wa_id'=>$adminId]);
        }
        $merchants = $query->all();
        return ArrayHelper::map($merchants,'id','name');
    }
}
