<?php

namespace admin\models;

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
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
class MerchantInfo extends ActiveRecord
{

    public $wa_username;
    public $wa_password;
    public $wa_type;
    public $wa_status;
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
            [['phone','name','contacter','wa_logo','wa_username','wa_password'],'required'],
            ['wa_password','match','pattern'=>'/^[\w\W]{5,16}$/','message'=>'密码长度为5~16位字母或数字'],
            ['wa_username','validUsername'],
            [['lat','lng','province','city','district','region'],'validPostion'],
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
            'contacter'=>'联系人',
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

    public function validPostion(){
        if(!empty($this->lat &&!empty($this->lng))){
            $query = self::find()->where(['lat'=>$this->lat,'lng'=>$this->lng,'is_active'=>1]);
            if(!empty($this->id)){
                $query->andWhere("id <> $this->id");
            }
            $model=$query->one();
            if(!empty($model)){
                $this->addError('address','该位置已存在一个激活中的商户');
            }
        }
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
    public function validUsername(){
        $query = Admin::find()->where(['wa_username'=>$this->wa_username]);
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

    public static function GetNames(){
        return array_values(ArrayHelper::getColumn(self::find()->addSelect('DISTINCT(`name`)')->all(),'name'));
    }

    public static function GetContacters(){
        return array_values(ArrayHelper::getColumn(self::find()->addSelect('DISTINCT(`contacter`)')->all(),'contacter'));
    }

    public static function GetPhones(){
        return array_values(ArrayHelper::getColumn(self::find()->addSelect('DISTINCT(`phone`)')->all(),'phone'));
    }

    public static function GetCities(){
        $shops = self::find()->all();
        return ArrayHelper::map($shops,'city','city');
    }

    public static function GetDistricts(){
        $shops = self::find()->all();
        return ArrayHelper::map($shops,'district','district');
    }

    public static function GetRegions(){
        $shops = self::find()->all();
        return ArrayHelper::map($shops,'region','region');
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

    /**
     * @var self $model
     * @return boolean
     */
    public function saveForm($model){
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $model->registe_at = time();
            $model->active_at = time();
            $model->is_active = 1;
            $model->wa_id = null;
            if(!$model->save()){
                throw new Exception('保存商户信息出错');
            }
            $admin = new Admin();
            $admin->attributes = [
                'wa_username'=>$model->wa_username,
                'wa_password'=>md5(Yii::$app->params['pwd_pre'].$model->wa_password),
                'wa_type'=>3,
                'target_id'=>$model->id,
                'created_time'=>date('Y-m-d H:i:s'),
                'updated_time'=>date('Y-m-d H:i:s'),
                'wa_phone'=>$model->phone,
                'wa_name'=>$model->name,
                'wa_token'=> Yii::$app->security->generateRandomString(),
                'wa_logo'=>$model->wa_logo,
                'wa_lock'=>0,
                'wa_status'=>1,
            ];
            if(!$admin->save()){
                throw new Exception('保存管理员信息出错');
            }
            $model->wa_id = $admin->wa_id;
            if(!$model->save()){
                throw new Exception('保存商户信息出错');
            }
            $user_id = $model->wa_id;
            $auth = Yii::$app->authManager;
            $role = $auth->createRole(Admin::getadminValue($admin->wa_type));                //创建角色对象
            $auth->assign($role, $user_id);
            $transaction->commit();
            return true;
        }catch (Exception $e){
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * @var self $model
     * @return boolean
     */
    public function updateForm($model){
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(!$model->save()){
                throw new Exception('保存商店信息出错');
            }
            if(!empty($model->wa_id)){
                $admin = Admin::findOne($model->wa_id);
                if(!empty($admin)&&$admin->wa_logo!=$model->wa_logo){
                    $admin->wa_logo=$model->wa_logo;
                    if(!$admin->save()){
                        throw new Exception('保存管理员信息出错');
                    }
                }
            }
            $transaction->commit();
            return true;
        }catch (Exception $e){
            $transaction->rollBack();
            return false;
        }
    }
}
