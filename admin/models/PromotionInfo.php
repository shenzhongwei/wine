<?php

namespace admin\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "promotion_info".
 *
 * @property integer $id
 * @property integer $pt_id
 * @property integer $style
 * @property integer $limit
 * @property integer $target_id
 * @property string $name
 * @property string $condition
 * @property string $discount
 * @property integer $valid_circle
 * @property integer $start_at
 * @property integer $end_at
 * @property integer $time
 * @property integer $regist_at
 * @property integer $is_active
 * @property integer $active_at
 *
 * @property PromotionType $pt
 * @property UserTicket[] $userTickets
 */
class PromotionInfo extends \yii\db\ActiveRecord
{
    public $date_valid;
    public $time_valid;
    public $circle_valid;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promotion_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pt_id', 'style', 'limit','date_valid','time_valid','circle_valid','target_id', 'valid_circle', 'start_at', 'end_at', 'time', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name','pt_id','style','limit','target_id','condition','discount','date_valid','time_valid','circle_valid'],'required'],
            [['condition', 'discount'], 'number'],
            [['name'], 'string', 'max' => 128],
            [['pt_id'], 'exist', 'skipOnError' => true, 'targetClass' => PromotionType::className(), 'targetAttribute' => ['pt_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'pt_id' => '促销种类',
            'style' => '优惠形式',
            'limit' => '适用范围',
            'target_id' => '适用对象',
            'name' => '活动名称',
            'condition' => '活动条件',
            'discount' => '活动优惠',
            'valid_circle' => '优惠券的有效期',
            'start_at' => '活动开始日期',
            'end_at' => '活动结束日期',
            'time' => '可参与次数',
            'regist_at' => '添加时间',
            'is_active' => '是否上架',
            'active_at' => '上架状态更改时间',
            'date_valid'=>'活动期限形式',
            'time_valid'=>'参与次数形式',
            'circle_valid'=>'优惠券期限形式',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPt()
    {
        return $this->hasOne(PromotionType::className(), ['id' => 'pt_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTickets()
    {
        return $this->hasMany(UserTicket::className(), ['pid' => 'id']);
    }

    public static function GetNames(){
        $res = self::find()->all();
        return array_values(ArrayHelper::getColumn($res,'name'));
    }

    public static function GetTypes(){
        $res = PromotionType::find()->joinWith('promotionInfos')->where('promotion_info.id>0')->all();
        return ArrayHelper::map($res,'id','name');
    }

    public static function GetLimits(){
        $res = Dics::findAll(['type'=>'优惠适用对象']);
        return ArrayHelper::map($res,'id','name');
    }

    public static function GetStyles($limit){
        if(empty($limit)){
            $res = Dics::findAll(['type'=>'优惠形式']);
        }else{
            $type = PromotionType::findOne($limit);
            if(!empty($type)){
                if(in_array($type->group,[1,3])){
                    $res = [
                        [
                            'id'=>1,
                            'name'=>'固定'
                        ],
                    ];
                }else{
                    $res = Dics::findAll(['type'=>'优惠形式']);
                }
            }else{
                $res = [];
            }
        }
        return ArrayHelper::map($res,'id','name');
    }

    public static function GetTargets($limit){
        switch ($limit){
            case 1;
                $result = [
                    '1'=>'平台通用',
                ];
                break;
            case 2;
                $res = MerchantInfo::find()->all();
                $result = ArrayHelper::map($res,'id','name');
                break;
            case 3;
                $res = ShopInfo::find()->all();
                $result = ArrayHelper::map($res,'id','name');
                break;
            case 4;
                $res = GoodInfo::find()->where('is_active=1')->all();
                $result = ArrayHelper::map($res,'id','name');
                break;
            default:
                $result=[];
                break;
        }
        return $result;
    }

    /*
     * 根据活动范围来获取对应的商家/门店/平台名称
     */
    public static function getNameByRange($model){
            switch($model->limit){
                case 1: //平台
                    $str='平台';
                    break;
                case 2: //商家
                    $str=MerchantInfoSearch::getOneMerchant($model->target_id);
                    break;
                case 3: //店铺
                    $str=ShopSearch::getOneShopname($model->target_id);
                    break;
                case 4: //某商品
                    $query=GoodInfo::findOne($model->target_id);
                    $str=empty($query)?'<span class="not-set">未设置</span>':$query->name;
                    break;
                default: $str='<span class="not-set">未设置</span>'; break;
            }
            return $str;
    }

    public static function getTargetsRange($type){
        switch($type){
            case 1: //平台
                $query =array(['id'=>'1','name'=>'平台']);
                break;
            case 2: //商家
                $query=MerchantInfo::find()->select(['id','name'])->where(['is_active'=>1])->all();
                break;
            case 3: //店铺
                $query=ShopInfo::find()->select(['id','name'])->where(['is_active'=>1])->all();
                break;
            case 4: //某商品
                $query=GoodInfo::find()->select(['id','name'])->where(['is_active'=>1])->all();
                break;
            default:
                $query =[];
                break;
        }
        $results=[];
        if(!empty($query)){
            $results = ArrayHelper::map($query,'id','name');
        }
        return $results;

    }
    /*
     * 有效期限
     */
    public static function getValidRange(){
        $validmodel=PromotionInfo::find()->select(['valid_circle'])->where(['is_active'=>1])->groupBy('valid_circle')->asArray()->all();
        $res=[];
        foreach($validmodel as $k=>$v){
            $res[$v['valid_circle']]=empty($v['valid_circle'])?'永久有效':$v['valid_circle'].'天';
        }
        return $res;
    }


    /**
     * 获取所有的类型
     */
    public static function getAllTypes($key){
        $query = PromotionType::find();
        if($key=='create'){
            $query->andWhere("is_active=1");
        }
        $res = $query->all();
        return ArrayHelper::map($res,'id','name');
    }

}
