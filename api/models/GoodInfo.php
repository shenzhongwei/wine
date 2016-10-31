<?php

namespace api\models;

use common\helpers\ArrayHelper;
use Yii;

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
 * @property integer $order
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $vip_show
 * @property integer $point_sup
 * @property integer $active_at
 *
 * @property CommentDetail[] $commentDetails
 * @property GoodCollection[] $goodCollections
 * @property GoodBoot $boot0
 * @property GoodBrand $brand0
 * @property GoodBreed $breed0
 * @property GoodColor $color0
 * @property GoodCountry $country0
 * @property GoodDry $dry0
 * @property MerchantInfo $merchant0
 * @property GoodSmell $smell0
 * @property GoodStyle $style0
 * @property GoodType $type0
 * @property GoodPic[] $goodPics
 * @property GoodRush $goodRush
 * @property GoodVip $goodVip
 * @property OrderDetail[] $orderDetails
 * @property ShoppingCert[] $shoppingCerts
 */
class GoodInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_info';
    }


    public $sum;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['merchant', 'type', 'brand', 'smell', 'color', 'dry', 'boot', 'breed', 'country', 'style', 'regist_at', 'point_sup','is_active', 'active_at','order','vip_show'], 'integer'],
            [['price','pro_price','vip_price','cost'], 'number'],
            [['detail'], 'string'],
            [['number'], 'string','max'=>8],
            [['name'], 'string', 'max' => 50],
            [['volum','pic'], 'string', 'max' => 128],
            [['unit','original_pay','vip_pay'], 'string', 'max' => 10],
            [['boot'], 'exist', 'skipOnError' => true, 'targetClass' => GoodBoot::className(), 'targetAttribute' => ['boot' => 'id']],
            [['brand'], 'exist', 'skipOnError' => true, 'targetClass' => GoodBrand::className(), 'targetAttribute' => ['brand' => 'id']],
            [['breed'], 'exist', 'skipOnError' => true, 'targetClass' => GoodBreed::className(), 'targetAttribute' => ['breed' => 'id']],
            [['color'], 'exist', 'skipOnError' => true, 'targetClass' => GoodColor::className(), 'targetAttribute' => ['color' => 'id']],
            [['country'], 'exist', 'skipOnError' => true, 'targetClass' => GoodCountry::className(), 'targetAttribute' => ['country' => 'id']],
            [['dry'], 'exist', 'skipOnError' => true, 'targetClass' => GoodDry::className(), 'targetAttribute' => ['dry' => 'id']],
            [['merchant'], 'exist', 'skipOnError' => true, 'targetClass' => MerchantInfo::className(), 'targetAttribute' => ['merchant' => 'id']],
            [['smell'], 'exist', 'skipOnError' => true, 'targetClass' => GoodSmell::className(), 'targetAttribute' => ['smell' => 'id']],
            [['style'], 'exist', 'skipOnError' => true, 'targetClass' => GoodStyle::className(), 'targetAttribute' => ['style' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => GoodType::className(), 'targetAttribute' => ['type' => 'id']],
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
        return $this->hasMany(CommentDetail::className(), ['gid' => 'id'])->where(['status'=>1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodCollections()
    {
        return $this->hasMany(GoodCollection::className(), ['gid' => 'id'])->where(['good_collection.status'=>1]);
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
    public function getType0()
    {
        return $this->hasOne(GoodType::className(), ['id' => 'type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodPics()
    {
        return $this->hasMany(GoodPic::className(), ['gid' => 'id'])->where(['good_pic.status'=>1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodRush()
    {
        return $this->hasOne(GoodRush::className(), ['gid' => 'id'])->where(['and','good_rush.is_active=1',"good_rush.start_at<=".time(),"good_rush.end_at>=".time()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodVip()
    {
        return $this->hasOne(GoodVip::className(), ['gid' => 'id'])->where(['good_vip.is_active'=>1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderDetails()
    {
        return $this->hasMany(OrderDetail::className(), ['gid' => 'id'])->joinWith('o')->where("order_info.state between 2 and 7 and order_date>=".strtotime(date('Y-m-01 00:00:00')));
    }
    /**
     * * @return \yii\db\ActiveQuery
     */
    public function getShoppingCerts()
    {
        return $this->hasMany(ShoppingCert::className(), ['gid' => 'id']);
    }

    /**
     * @param $page
     * @return array
     * 首页热销产品
     */
    public static function GoodList($page){
        $pageSize = Yii::$app->params['pageSize'];
        $query = self::find()->joinWith(['merchant0','type0'])
            ->leftJoin("(SELECT a.* FROM order_detail a LEFT JOIN order_info b ON a.oid=b.id 
            WHERE state between 2 and 7 and order_date>=".strtotime(date('Y-m-01 00:00:00',time())).") c",'good_info.id=c.gid')
            ->where('good_info.is_active=1 and merchant>0 
            and merchant_info.id>0 and merchant_info.is_active=1 
            and good_info.type>0 and good_type.is_active=1 and good_type.id>0');
        $query->addSelect(['good_info.*','IFNULL(sum(c.amount),0) as sum'])->groupBy(['good_info.id'])
            ->orderBy(['sum'=>SORT_DESC,'order'=>SORT_ASC]);
        $count = $query->count();
        $query->offset(($page-1)*$pageSize)->limit($pageSize);
        $res = $query->all();
        $result = self::HotData($res);
        return [$result,$count];
    }

    public static function HotData($arr=[]){
        $res = ArrayHelper::getColumn($arr,function($element){
            $payArr = explode('|',$element->original_pay);
            return [
                'good_id'=>$element->id,
                'pic'=>Yii::$app->params['img_path'].$element->pic,
                'name'=>$element->name,
                'volum'=>$element->volum,
                'number'=>$element->number,
                'sale_price'=>$element->pro_price,
                'original_price'=>$element->price,
                'unit'=>$element->unit,
                'cash_pay'=>in_array('1',$payArr) ? 1:0,
                'ali_pay'=>in_array('2',$payArr) ? 1:0,
                'we_pay'=>in_array('3',$payArr) ? 1:0,
                'point_sup'=>$element->point_sup,
                'sale'=>$element->sum,
                'type'=>1,
                'operate'=>1,
            ];
        });
        return $res;
    }

    public static function RushData($arr=[]){
        $res = ArrayHelper::getColumn($arr,function($element){
            if(!empty($element->uid)){
                $order = OrderDetail::find()->joinWith('o')->addSelect(["SUM(amount) as sum"])
                    ->where("type=3 and state between 2 and 7 and uid=$element->uid and gid=".$element->gid." 
                and rush_id=$element->id and order_date>=$element->start_at and order_date<=$element->end_at")->one();
                $buyNum =$order->sum;
                $rest = $element->limit-$buyNum;
            }else{
                $rest = $element->limit;
            }
            $payArr = explode('|',$element->rush_pay);
            return [
                'good_id'=>$element->gid,
                'pic'=>Yii::$app->params['img_path'].$element->g->pic,
                'name'=>$element->g->name,
                'volum'=>$element->g->volum,
                'number'=>$element->g->number,
                'amount'=>$element->amount,
                'limit'=>$element->limit,
                'rest'=>$rest<0 ? 0:$rest,
                'end_at'=>date('Y-m-d H:i:s',$element->end_at),
                'sale_price'=>$element->price,
                'original_price'=>$element->g->price,
                'unit'=>$element->g->unit,
                'cash_pay'=>in_array('1',$payArr) ? 1:0,
                'ali_pay'=>in_array('2',$payArr) ? 1:0,
                'we_pay'=>in_array('3',$payArr) ? 1:0,
                'point_sup'=>$element->point_sup,
                'type'=>3,
                'operate'=>1,
            ];
        });
        return $res;
    }

    public static function VipData($arr=[],$from){
        if(empty($from)){
            $res = ArrayHelper::getColumn($arr,function($element){
                return [
                    'good_id'=>$element->id,
                    'pic'=>Yii::$app->params['img_path'].$element->pic,
                    'name'=>$element->name,
                    'volum'=>$element->volum,
                    'number'=>$element->number,
                    'sale_price'=>$element->vip_price,
                    'original_price'=>$element->price,
                    'unit'=>$element->unit,
                    'type'=>2,
                    'operate'=>0,
                ];
            });
        }else{
            $res = ArrayHelper::getColumn($arr,function($element){
                $payArr = explode('|',$element->vip_pay);
                return [
                    'good_id'=>$element->id,
                    'pic'=>Yii::$app->params['img_path'].$element->pic,
                    'name'=>$element->name,
                    'volum'=>$element->volum,
                    'number'=>$element->number,
                    'sale_price'=>$element->vip_price,
                    'original_price'=>$element->price,
                    'unit'=>$element->unit,
                    'cash_pay'=>in_array('1',$payArr) ? 1:0,
                    'ali_pay'=>in_array('2',$payArr) ? 1:0,
                    'we_pay'=>in_array('3',$payArr) ? 1:0,
                    'point_sup'=>$element->point_sup,
                    'type'=>2,
//                    'sale'=>$element->sum,
                    'operate'=>1,
                ];
            });
        }
        return $res;
    }
    /**
     * @param array $arr
     * @return array
     * 处理model
     */
    public static function data($arr=[]){
        $res = ArrayHelper::getColumn($arr,function($element){
            $payArr = explode('|',$element->vip_pay);
            return [
                'good_id'=>$element->id,
                'pic'=>Yii::$app->params['img_path'].$element->pic,
                'name'=>$element->name,
                'volum'=>$element->volum,
                'number'=>$element->number,
                'sale_price'=>$element->pro_price,
                'original_price'=>$element->price,
                'unit'=>$element->unit,
                'cash_pay'=>in_array('1',$payArr) ? 1:0,
                'ali_pay'=>in_array('2',$payArr) ? 1:0,
                'we_pay'=>in_array('3',$payArr) ? 1:0,
                'point_sup'=>$element->point_sup,
                'type'=>1,
                'operate'=>1,
            ];
        });
        return $res;
    }
}
