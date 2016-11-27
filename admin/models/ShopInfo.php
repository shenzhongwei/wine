<?php

namespace admin\models;

use common\helpers\ArrayHelper;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "shop_info".
 *
 * @property integer $id
 * @property string $name
 * @property integer $wa_id
 * @property integer $merchant
 * @property string $region
 * @property string $contacter
 * @property string $phone
 * @property string $address
 * @property integer $lat
 * @property integer $lng
 * @property integer $limit
 * @property string $least_money
 * @property string $send_bill
 * @property string $no_send_need
 * @property string $bus_pic
 * @property string $logo
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $active_at
 * @property string $province
 * @property string $city
 * @property string $district
 *
 * @property OrderInfo[] $orderInfos
 * @property Admin $wa
 * @property MerchantInfo $merchant0
 */
class ShopInfo extends \yii\db\ActiveRecord
{
    public $wa_username;
    public $wa_password;
    public $wa_status;
    public $img;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wa_id', 'merchant', 'lat', 'lng', 'limit', 'regist_at', 'is_active', 'active_at','wa_status'], 'integer'],
            [['least_money', 'send_bill', 'no_send_need'], 'number'],
            [['name', 'region'], 'string', 'max' => 50],
            [['phone','contacter'],'string'],
            [['address', 'bus_pic', 'logo', 'province', 'city', 'district'], 'string', 'max' => 128],
            [['wa_id'], 'exist', 'skipOnError' => true, 'targetClass' =>Admin::className(), 'targetAttribute' => ['wa_id' => 'wa_id']],
            [['merchant'], 'exist', 'skipOnError' => true, 'targetClass' => MerchantInfo::className(), 'targetAttribute' => ['merchant' => 'id']],
            [['merchant','limit','least_money','send_bill','name','phone','region','address',
                'contacter','no_send_need','wa_username','wa_password','bus_pic','logo',
//            'province','city','district','lat','lng'
            ],'required'],
            ['wa_password','match','pattern'=>'/^[\w\W]{5,16}$/','message'=>'密码长度为5~16位'],
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
            'id' => 'ID',
            'name' => '门店名称',
            'wa_id' => '后台管理员id',
            'merchant' => '所属商户',
            'region' => '所在地区',
            'address' => '详细地址',
            'lat' => '纬度',
            'lng' => '经度',
            'limit' => '配送范围',
            'least_money' => '订单最低金额',
            'send_bill' => '配送金额',
            'no_send_need' => '免配送条件',
            'bus_pic' => '营业执照',
            'contacter'=>'联系人',
            'wa_status'=>'后台状态',
            'logo' => '门店logo',
            'regist_at' => '入驻时间',
            'is_active' => '是否激活',
            'active_at' => '激活状态更改时间',
            'province' => '省',
            'city' => '市',
            'district' => '区',
            'phone'=>'联系电话',
            'wa_username'=>'后台登陆名',
            'wa_password'=>'后台登陆密码',
            'img'=>'营业执照',
            'url'=>'门店logo',
        ];
    }

    //指定“新增” 模块需要验证参数的规则,里面添加的参数是 form表单中的值
//    public function scenarios()
//    {
//        $n=parent::scenarios();
//        $n['create']=['wa_id','merchant','phone','name','limit','least_money','send_bill','no_send_need','bus_pic','logo','province','city','district','region','address','registe_at','active_at','lat','lng'];
//        return $n;
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfos()
    {
        return $this->hasMany(OrderInfo::className(), ['sid' => 'id']);
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
    public function getMerchant0()
    {
        return $this->hasOne(MerchantInfo::className(), ['id' => 'merchant']);
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

    //判断名称是否唯一
    public function validPostion(){
        if(empty($this->lat)||empty($this->lng)||empty($this->province)||empty($this->city)||empty($this->district)){
            $this->addError('region','位置信息有误，请重新选择');
        }
        if(!empty($this->lat &&!empty($this->lng))){
            $query = self::find()->where(['lat'=>$this->lat,'lng'=>$this->lng,'is_active'=>1]);
            if(!empty($this->id)){
                $query->andWhere("id <> $this->id");
            }
            $model=$query->one();
            if(!empty($model)){
                $this->addError('address','该位置已存在一个激活中的店铺');
            }
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

    public static function GetMerchants($str=''){
        $query = MerchantInfo::find();
        if(empty($str)){
            $query->andWhere("id in (SELECT `merchant` FROM shop_info)");
        } elseif($str=='create'){
            $query->andWhere("is_active=1");
        } else{

        }
        $merchants = $query->all();
        return ArrayHelper::map($merchants,'id','name');
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

    /**
     * @var self $model
     * @return boolean
     */
    public function saveForm($model){
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $model->regist_at = time();
                $model->active_at = time();
                $model->is_active = 1;
                $model->wa_id = null;
                if(!$model->save()){
                    throw new Exception('保存商店信息出错');
                }
                $admin = new Admin();
                $admin->attributes = [
                    'wa_username'=>$model->wa_username,
                    'wa_password'=>md5(Yii::$app->params['pwd_pre'].$model->wa_password),
                    'wa_type'=>4,
                    'target_id'=>$model->id,
                    'created_time'=>date('Y-m-d H:i:s'),
                    'updated_time'=>date('Y-m-d H:i:s'),
                    'wa_phone'=>$model->phone,
                    'wa_name'=>$model->name,
                    'wa_token'=> Yii::$app->security->generateRandomString(),
                    'wa_logo'=>$model->logo,
                    'wa_lock'=>0,
                    'wa_status'=>1,
                ];
                if(!$admin->save()){
                    throw new Exception('保存管理员信息出错');
                }
                $model->wa_id = $admin->wa_id;
                if(!$model->save()){
                    throw new Exception('保存商店信息出错');
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
                if(!empty($admin)&&$admin->wa_logo!=$model->logo){
                    $admin->wa_logo=$model->logo;
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
