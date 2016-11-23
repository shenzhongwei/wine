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
            [['pt_id','date_valid','start_at','end_at','condition', 'discount','limit','target_id','style'],'validType'],
            [['start_at','end_at','date_valid'],'validDate'],
            [['time_valid','time'],'validTime'],
            [['circle_valid','valid_circle'], 'validCircle'],
            [['name'], 'string', 'max' => 128],
            [['condition','discount',],'number'],
            [['pt_id'], 'exist', 'skipOnError' => true, 'targetClass' => PromotionType::className(), 'targetAttribute' => ['pt_id' => 'id']],
        ];
    }

    //验证促销
    public function validType(){
        if(!empty($this->pt_id)){
            $promotionType = PromotionType::findOne($this->pt_id);
            if(empty($promotionType)){
                return $this->addError('pt_id','促销种类异常');
            }else{
                $message='';
                $query = self::find()->joinWith(['pt'])->where("promotion_info.is_active=1");
                if(!empty($this->id)){
                    $query->andWhere("promotion_info.id<>$this->id");
                }
                if(in_array($promotionType->env,[1,2,4])||in_array($promotionType->group,[3])){
                    if(in_array($promotionType->env,[1,2,4])){
                        $message = $promotionType->env==1 ? '注册类型的活动有效期内只能存在一个，请勿重复添加':
                            ($promotionType->env==2 ? '推荐用户注册的活动有效期内只能存在一个，请勿重复添加':
                                '分享送券的活动有效期内只能存在一个，请勿重复添加');
                        $query->andWhere("promotion_type.env=$promotionType->env");
                    }
                    if(in_array($promotionType->group,[3])){
                        $message = '充值开会员的活动有效期内只能存在一个，请勿重复添加';
                        $query->andWhere("promotion_type.group=$promotionType->group");
                    }
                    if(!empty($this->start_at)){
                        $query->andWhere("(start_at<=$this->start_at and end_at>=$this->start_at) or (start_at=0 and end_at=0)");
                    }
                    if(!empty($this->end_at)){
                        $query->andWhere("(start_at<=$this->end_at and end_at>=$this->end_at) or (start_at=0 and end_at=0)");
                    }
                    if($this->date_valid==0&&$this->date_valid!=''){
                        $query->andWhere("(start_at<=".time()." and end_at>=".time().") or (start_at=0 and end_at=0)");
                    }
                    $model = $query->one();
                    if(!empty($model)){
                        return $this->addError('pt_id',$message);
                    }
                }
                if($this->style==1){
                    if($promotionType->group==3){
                        if($this->condition!=0 &&$this->condition==''){
                            return $this->addError('condition','请填写开通会员条件');
                        }
                        if($this->condition<0){
                            return $this->addError('condition','充值条件不能于0');
                        }
                    }elseif (in_array($promotionType->group,[1,5])){
                        if($this->discount!=0 &&$this->discount==''){
                            return $this->addError('discount','请填写优惠券的优惠额');
                        }
                        if($this->discount<=0){
                            return $this->addError('discount','优惠额必须大于0');
                        }
                        if($this->condition!=0 &&$this->condition==''){
                            return $this->addError('condition','请填写优惠券的使用条件');
                        }
                        if($this->condition<0){
                            return $this->addError('condition','使用条件不能小于0');
                        }
                    }elseif ($promotionType->group==2){
                        if(in_array($promotionType->env,[2,5])){
                            if($this->discount!=0 &&$this->discount==''){
                                return $this->addError('discount','请填写积分数');
                            }
                            if($this->discount<=0){
                                return $this->addError('discount','积分数必须大于0');
                            }
                        }elseif ($promotionType->env==6){
                            if($this->discount!=0 &&$this->discount==''){
                                return $this->addError('discount','请填写积分数');
                            }
                            if($this->discount<=0){
                                return $this->addError('discount','积分数必须大于0');
                            }
                            if($this->condition!=0 &&$this->condition==''){
                                return $this->addError('condition','请填写赠送积分的条件');
                            }
                            if($this->condition<0){
                                return $this->addError('condition','条件不能小于0');
                            }
                        }
                    }else{
                        if($this->discount!=0 &&$this->discount==''){
                            return $this->addError('discount','请填写促销的优惠额');
                        }
                        if($this->discount<=0){
                            return $this->addError('discount','优惠额必须大于0');
                        }
                        if($this->condition!=0 &&$this->condition==''){
                            return $this->addError('condition','请填写促销的使用条件');
                        }
                        if($this->condition<0){
                            return $this->addError('condition','使用条件比不能小于0');
                        }
                    }
                }elseif($this->style==2){
                    if($this->discount!=0 &&$this->discount==''){
                        return $this->addError('discount','优惠请填写百分比');
                    }
                    if($this->discount<=0){
                        return $this->addError('discount','百分比必须大于0');
                    }
                }
                if($promotionType->group==1 ||$promotionType->group==5){
                    if($this->circle_valid==''&&$this->circle_valid<>0){
                        $this->addError('circle_valid','请选择优惠券的有效期形式');
                    }
                }
                if(!empty($this->limit)&&!empty($this->target_id)&&!empty($this->style)){
                    $query2 = self::find()->joinWith('pt')->where("promotion_info.is_active=1");
                    if(!empty($this->id)){
                        $query2->andWhere("promotion_info.id<>$this->id");
                    }
                    if(!empty($this->start_at)){
                        $query2->andWhere("(start_at<=$this->start_at and end_at>=$this->start_at) or (start_at=0 and end_at=0)");
                    }
                    if(!empty($this->end_at)){
                        $query2->andWhere("(start_at<=$this->end_at and end_at>=$this->end_at) or (start_at=0 and end_at=0)");
                    }
                    if($this->date_valid==0&&$this->date_valid!=''){
                        $query2->andWhere("(start_at<=".time()." and end_at>=".time().") or (start_at=0 and end_at=0)");
                    }
                    $query2->andWhere("promotion_info.limit=$this->limit and promotion_info.target_id=$this->target_id and pt_id=$this->pt_id");
//                    var_dump($promotionType);
//                    exit;
                    if($promotionType->env==5){
                        $query2->andWhere("promotion_type.env=$promotionType->env");
//                        var_dump($query2->one()->toArray());
//                        exit;
                        $sttr = 'target_id';
                        $message2 = '该范围对象已存在一个推荐下单活动请勿重复添加';
                    }else{
                        $query2->andWhere("promotion_info.style<>$this->style");
                        $str = $this->style == 1 ? '百分比':'固定';
                        $sttr = 'style';
                        $message2 = "该种类已存在".$str."形式的优惠活动，请勿添加该类型的优惠活动";
                    }
                    $model2= $query2->one();
                    if(!empty($model2)){
                        return $this->addError($sttr,$message2);
                    }
                    $query3 = self::find()->joinWith('pt')->where("promotion_info.is_active=1 and promotion_info.limit=$this->limit 
                        and pt_id=$this->pt_id and style=$this->style and target_id = $this->target_id");
                    if(!empty($this->id)){
                        $query3->andWhere("promotion_info.id<>$this->id");
                    }
                    if(!empty($this->start_at)){
                        $query3->andWhere("(start_at<=$this->start_at and end_at>=$this->start_at) or (start_at=0 and end_at=0)");
                    }
                    if(!empty($this->end_at)){
                        $query3->andWhere("(start_at<=$this->end_at and end_at>=$this->end_at) or (start_at=0 and end_at=0)");
                    }
                    if($this->date_valid==0&&$this->date_valid!=''){
                        $query3->andWhere("(start_at<=".time()." and end_at>=".time().") or (start_at=0 and end_at=0)");
                    }
                    if($this->style==1){
                        if($promotionType->group==3){
                            $query3->andWhere("`condition`=$this->condition");
                            $attr = 'condition';
                            $message3 = '已存在一样类型的活动，请勿重复添加';
                        }elseif (in_array($promotionType->group,[1,5])){
                            $query3->andWhere("`condition`=$this->condition and discount = $this->discount");
                            $attr = 'condition';
                            $message3 = '已存在一样类型的活动，请勿重复添加';
                        }elseif ($promotionType->group==2){
                            if(in_array($promotionType->env,[2,5])){
                                $query3->andWhere("discount = $this->discount");
                                $attr = 'discount';
                                $message3 = '已存在一样类型的活动，请勿重复添加';
                            }else{
                                $query3->andWhere("`condition`=$this->condition and discount = $this->discount");
                                $attr = 'condition';
                                $message3 = '已存在一样类型的活动，请勿重复添加';
                            }
                        }else{
                            $query3->andWhere("`condition`=$this->condition and discount = $this->discount");
                            $attr = 'condition';
                            $message3 = '已存在一样类型的活动，请勿重复添加';
                        }
                    }else{
                        $query3->andWhere("discount=$this->discount");
                        $attr = 'discount';
                        $message3 = '已存在一样类型的活动，请勿重复添加';
                    }
                    $model3 = $query3->one();
                    if(!empty($model3)){
                        return $this->addError($attr,$message3);
                    }
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
        if ($this->circle_valid == 1) {
            if ($this->valid_circle <= 0) {
                $this->addError('valid_circle', '有效期天数比需大于0');
            }
            if (empty($this->valid_circle)) {
                $this->addError('valid_circle', '请填写优惠券有效期天数');
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
                if(in_array($type->group,[1,3,5])||in_array($type->env,[2])){
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
                $res = GoodType::find()->all();
                $result = ArrayHelper::map($res,'id','name');
                break;
            case 3;
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
                case 2: //种类
                    $str=GoodType::getOneType($model->target_id);
                    break;
                case 3: //商品
                    $str=GoodInfo::getGoodName($model->target_id);
                    break;
                default: $str='<span class="not-set">未设置</span>'; break;
            }
            return $str;
    }

    public static function getTargetsRange($type){
        switch($type){
            case 1: //平台
                $query =array(['id'=>'1','name'=>'平台通用']);
                break;
            case 2: //大类
                $query=GoodType::find()->select(['id','name'])->where(['is_active'=>1])->all();
                break;
            case 3: //商品
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
     *
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
        $query = PromotionType::find()->where("class<>2");
        if($key=='create'){
            $query->andWhere("is_active=1");
        }
        $res = $query->all();
        return ArrayHelper::map($res,'id','name');
    }

}
