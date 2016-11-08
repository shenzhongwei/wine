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
            [['pt_id', 'style', 'limit','date_valid','time_valid','circle_valid','target_id', 'valid_circle', 'time', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['name','pt_id','style','limit','target_id','date_valid','time_valid'],'required'],
            [['pt_id','date_valid','start_at','end_at'],'validType'],
            [['condition', 'discount','pt_id','start_at','end_at','date_valid','limit','target_id','style'],'validNum'],
            [['start_at','end_at','date_valid'],'validDate'],
            [['time_valid','time'],'validTime'],
            [['pt_id','circle_valid','valid_circle'], 'validCircle'],
            [['name'], 'string', 'max' => 128],
            [['condition','discount',],'number'],
            [['pt_id'], 'exist', 'skipOnError' => true, 'targetClass' => PromotionType::className(), 'targetAttribute' => ['pt_id' => 'id']],
        ];
    }

    //验证类型
    public function validType(){
        if(!empty($this->pt_id)){
            $type = PromotionType::findOne($this->pt_id);
            if(empty($type)){
                $this->addError('pt_id','异常类型');
            }else{
                if(in_array($type->env,[1,2,4,5])||$type->group == 3){
                    $query = self::find()->joinWith('pt')->where("
            promotion_type.id>0 and promotion_info.id>0 and promotion_info.is_active=1");
                    if(in_array($type->env,[1,2,4,5])){
                        $query->andWhere("promotion_type.env=$type->env ");
                    }
                    if ($type->group == 3){
                        $query->andWhere("promotion_type.group=$type->group");
                    }
                    if(!empty($this->id)){
                        $query->andWhere("promotion_info.id<>$this->id");
                    }
                    if(!empty($this->start_at)){
                        $query->andWhere("(start_at<=$this->start_at and end_at>=$this->start_at) or (start_at=0 and end_at=0)");
                    }
                    if(!empty($this->end_at)){
                        $query->andWhere("(start_at<=$this->end_at and end_at>=$this->end_at) or (start_at=0 and end_at=0)");
                    }
                    if($this->date_valid==0){
                        $query->andWhere("(start_at<=".time()." and end_at>=".time().") or (start_at=0 and end_at=0)");
                    }
                    $model = $query->one();
                    if(!empty($model)){
                        $this->addError('pt_id','该类别的促销环境有效期内只能存在唯一一个有效活动，请勿重复添加');
                    }
                }
            }
        }
    }
    //验证优惠和条件
    public function validNum(){
        if(!empty($this->pt_id)) {
            $type = PromotionType::findOne($this->pt_id);
            if (empty($type)) {
                $this->addError('pt_id', '异常类型');
            }
            if ($this->style == 1) {
                if ($type->group != 3) {
                    if (empty($this->discount)) {
                        $this->addError('discount', '请填写优惠额度');
                    }
                }
                if($type->env!=2||$type->group!=2){
                    if ($this->condition===''||$this->condition===null) {
                        $this->addError('condition', '请填写优惠条件');
                    }
                }
            } else {
                if (empty($this->discount)) {
                    $this->addError('discount', '请填写优惠额度百分比');
                }
                if (empty($this->discount) > 100) {
                    $this->addError('discount', '额度百分比不可超出100');
                }
            }
            if (!empty($this->condition) && $this->style==1) {
                $query = self::find()->where("
            is_active=1 and `condition`=$this->condition and pt_id=$this->pt_id and 
            `limit`=$this->limit and target_id=$this->target_id");
                if (!empty($this->id)) {
                    $query->andWhere("id<>$this->id");
                }
                if (!empty($this->start_at)) {
                    $query->andWhere("(start_at<=$this->start_at and end_at>=$this->start_at) or (start_at=0 and end_at=0)");
                }
                if (!empty($this->end_at)) {
                    $query->andWhere("(start_at<=$this->end_at and end_at>=$this->end_at) or (start_at=0 and end_at=0)");
                }
                if ($this->date_valid == 0) {
                    $query->andWhere("(start_at<=" . time() . " and end_at>=" . time() . ") or (start_at=0 and end_at=0)");
                }
                $model = $query->one();
                if (!empty($model)) {
                    $this->addError('condition', '已存在一个相同类型条件相同的优惠，请勿重复添加');
                }
            }
            if (!empty($this->discount) && $this->style==2) {
                $query = self::find()->where("
            is_active=1 and discount=$this->discount and pt_id=$this->pt_id and 
            `limit`=$this->limit and target_id=$this->target_id");
                if (!empty($this->id)) {
                    $query->andWhere("id<>$this->id");
                }
                if (!empty($this->start_at)) {
                    $query->andWhere("(start_at<=$this->start_at and end_at>=$this->start_at) or (start_at=0 and end_at=0)");
                }
                if (!empty($this->end_at)) {
                    $query->andWhere("(start_at<=$this->end_at and end_at>=$this->end_at) or (start_at=0 and end_at=0)");
                }
                if ($this->date_valid == 0) {
                    $query->andWhere("(start_at<=" . time() . " and end_at>=" . time() . ") or (start_at=0 and end_at=0)");
                }
                $model = $query->one();
                if (!empty($model)) {
                    $this->addError('condition', '已存在一个相同类型百分比相同的优惠，请勿重复添加');
                }
            }
        }
    }
    //验证日期
    public function validDate(){
        if($this->date_valid==1){
            if(empty($this->start_at)){
                $this->addError('start_at','请选择开始日期');
            }
            if(empty($this->end_at)){
                $this->addError('end_at','请选择结束日期');
            }
        }
    }
    //验证次数
    public function validTime(){
        if($this->time_valid==1){
            if($this->time<=0){
                $this->addError('time','可参与次数比需大于0');
            }
            if(empty($this->time)){
                $this->addError('time','请填写可参与次数');
            }
        }
    }

    //验证有效期
    public function validCircle(){

        if(!empty($this->pt_id)) {
            $type = PromotionType::findOne($this->pt_id);
            if (empty($type)) {
                $this->addError('pt_id', '异常类型');
            } else {
                if ($type->group == 1) {
                    if ($this->circle_valid===''||$this->circle_valid===null) {
                        $this->addError('circle_valid', '优惠券有效期形式不能为空');
                    }
                    if ($this->circle_valid == 1) {
                        if ($this->valid_circle <= 0) {
                            $this->addError('valid_circle', '有效期天数比需大于0');
                        }
                        if (empty($this->valid_circle)) {
                            $this->addError('valid_circle', '请填写优惠券有效期天数');
                        }
                    }
                }
            }
        }
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
                if(in_array($type->group,[1,3])||$type->env==2){
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
                    $str='平台通用';
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
