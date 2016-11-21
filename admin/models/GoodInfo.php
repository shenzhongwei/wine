<?php

namespace admin\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "good_info".
 *
 * @property integer $id
 * @property integer $merchant
 * @property integer $type
 * @property integer $brand
 * @property integer $smell
 * @property integer $color
 * @property integer $dry
 * @property integer $boot
 * @property integer $breed
 * @property integer $country
 * @property integer $style
 * @property string $name
 * @property string $volum
 * @property string $price
 * @property string $pro_price
 * @property string $original_pay
 * @property string $vip_price
 * @property string $vip_pay
 * @property string $unit
 * @property string $pic
 * @property string $cost
 * @property string $number
 * @property string $detail
 * @property integer $point_sup
 * @property integer $order
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $vip_show
 * @property integer $active_at
 *
 * @property CommentDetail[] $commentDetails
 * @property GoodCollection[] $goodCollections
 * @property GoodBoot $boot0
 * @property GoodType $type0
 * @property GoodBrand $brand0
 * @property GoodBreed $breed0
 * @property GoodColor $color0
 * @property GoodCountry $country0
 * @property GoodDry $dry0
 * @property MerchantInfo $merchant0
 * @property GoodSmell $smell0
 * @property GoodStyle $style0
 * @property GoodPic[] $goodPics
 * @property GoodRush[] $goodRushes
 * @property GoodVip[] $goodVips
 * @property OrderDetail[] $orderDetails
 * @property ShoppingCert[] $shoppingCerts
 */
class GoodInfo extends \yii\db\ActiveRecord
{
    public $img;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_info';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['merchant', 'type', 'brand', 'smell', 'point_sup','vip_show', 'color', 'dry', 'boot', 'breed', 'country', 'style', 'order', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name', 'detail','type','price','detail','volum','unit','merchant','pro_price','cost','original_pay','vip_pay','vip_show','point_sup','vip_price'], 'required'],
            [['price','pro_price','cost','vip_price'],'compare','compareValue'=>0,'operator'=>'>'],
            [['price','pro_price','cost'],'validPrice'],
            [['pic'],'required','message'=>'请上传产品图片'],
            [['price','pro_price','cost','vip_price'], 'number'],
            [['detail'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['volum'], 'string', 'max' => 128],
            [['unit','original_pay','vip_pay'], 'string', 'max' => 10],
            [['number'], 'string', 'max' => 10],
            ['img', 'file', 'extensions' => ['png', 'jpg', 'gif','jpeg','ico'], 'maxSize' => 1024*1024*6],
            [['boot'], 'exist', 'skipOnError' => true, 'targetClass' => GoodBoot::className(), 'targetAttribute' => ['boot' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => GoodType::className(), 'targetAttribute' => ['type' => 'id']],
            [['brand'], 'exist', 'skipOnError' => true, 'targetClass' => GoodBrand::className(), 'targetAttribute' => ['brand' => 'id']],
            [['breed'], 'exist', 'skipOnError' => true, 'targetClass' => GoodBreed::className(), 'targetAttribute' => ['breed' => 'id']],
            [['color'], 'exist', 'skipOnError' => true, 'targetClass' => GoodColor::className(), 'targetAttribute' => ['color' => 'id']],
            [['country'], 'exist', 'skipOnError' => true, 'targetClass' => GoodCountry::className(), 'targetAttribute' => ['country' => 'id']],
            [['dry'], 'exist', 'skipOnError' => true, 'targetClass' => GoodDry::className(), 'targetAttribute' => ['dry' => 'id']],
            [['merchant'], 'exist', 'skipOnError' => true, 'targetClass' => MerchantInfo::className(), 'targetAttribute' => ['merchant' => 'id']],
            [['smell'], 'exist', 'skipOnError' => true, 'targetClass' => GoodSmell::className(), 'targetAttribute' => ['smell' => 'id']],
            [['style'], 'exist', 'skipOnError' => true, 'targetClass' => GoodStyle::className(), 'targetAttribute' => ['style' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'merchant' => '所属商户',
            'type' => '类别',
            'brand' => '品牌',
            'smell' => '香型',
            'color' => '颜色类型',
            'dry' => '干型',
            'boot' => '产地',
            'breed' => '品种',
            'country' => '国家',
            'style' => '类型',
            'name' => '商品名',
            'volum' => '容量',
            'price' => '原价',
            'pro_price'=> '优惠价',
            'vip_pay'=>'会员支付方式',
            'original_pay'=>'一般支付方式',
            'cost'=>'成本价',
            'unit' => '单位',
            'pic' => '产品图片',
            'vip_show'=>'会员列表展示',
            'img'=>'产品图片',
            'vip_price'=>'会员价',
            'number' => '编号',
            'detail' => '详情',
            'order' => '排序',
            'point_sup'=>'积分支持',
            'regist_at' => '添加时间',
            'is_active' => '是否上架',
            'active_at' => '上架状态更改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommentDetails()
    {
        return $this->hasMany(CommentDetail::className(), ['gid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodCollections()
    {
        return $this->hasMany(GoodCollection::className(), ['gid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoot0()
    {
        return $this->hasOne(GoodBoot::className(), ['id' => 'boot']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(GoodType::className(), ['id' => 'type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand0()
    {
        return $this->hasOne(GoodBrand::className(), ['id' => 'brand']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBreed0()
    {
        return $this->hasOne(GoodBreed::className(), ['id' => 'breed']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColor0()
    {
        return $this->hasOne(GoodColor::className(), ['id' => 'color']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry0()
    {
        return $this->hasOne(GoodCountry::className(), ['id' => 'country']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDry0()
    {
        return $this->hasOne(GoodDry::className(), ['id' => 'dry']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerchant0()
    {
        return $this->hasOne(MerchantInfo::className(), ['id' => 'merchant']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSmell0()
    {
        return $this->hasOne(GoodSmell::className(), ['id' => 'smell']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStyle0()
    {
        return $this->hasOne(GoodStyle::className(), ['id' => 'style']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodPics()
    {
        return $this->hasMany(GoodPic::className(), ['gid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodRushes()
    {
        return $this->hasMany(GoodRush::className(), ['gid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodVips()
    {
        return $this->hasMany(GoodVip::className(), ['gid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderDetails()
    {
        return $this->hasMany(OrderDetail::className(), ['gid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShoppingCerts()
    {
        return $this->hasMany(ShoppingCert::className(), ['gid' => 'id']);
    }

    public function validPrice(){
        if($this->price<$this->pro_price){
            $this->addError('price','优惠价不得高于原价');
        }elseif($this->price<$this->cost){
            $this->addError('price','成本价不得高于原价');
        }elseif($this->pro_price<$this->cost){
            $this->addError('price','成本价不得高于优惠价');
        }elseif ($this->vip_price>$this->pro_price){
            $this->addError('vip_price','会员价不得高于优惠价');
        }
    }

    public static function GetGoodNames(){
        $admin = Yii::$app->user->identity;
        $adminType = $admin->wa_type;
        $adminId = $admin->wa_id;
        $query = self::find();
        if($adminType>2){
            $manager = MerchantInfo::findOne(['wa_id'=>$adminId]);
            $query->andWhere(['merchant'=>empty($manager) ? 0:$manager->id]);
        }
        $goods = $query->all();
        return array_values(array_unique(ArrayHelper::getColumn($goods,'name')));
    }

    public static function GetGoodNumbers(){
        $admin = Yii::$app->user->identity;
        $adminType = $admin->wa_type;
        $adminId = $admin->wa_id;
        $query = self::find();
        if($adminType>2){
            $manager = MerchantInfo::findOne(['wa_id'=>$adminId]);
            $query->andWhere(['merchant'=>empty($manager) ? 0:$manager->id]);
        }
        $goods = $query->asArray()->all();
        return ArrayHelper::getColumn($goods,'number');
    }

    public static function generateCode(){
        $arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $key = array_rand($arr); //array_rand() 函数返回数组中的随机键
        $code=$arr[$key];
        return $code;
    }


    //根据商品id查找商品名称
    public static function getGoodName($id){
        $model=GoodInfo::findOne($id);
        return empty($model)?'<span class="not-set">未设置</span>':$model->name;
    }
}
