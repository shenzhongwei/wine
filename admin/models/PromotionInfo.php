<?php

namespace admin\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "promotion_info".
 *
 * @property integer $id
 * @property integer $pt_id
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
    public $start_from;
    public $end_to;
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
            [['pt_id', 'limit', 'target_id', 'valid_circle', 'start_at', 'end_at', 'time', 'regist_at', 'is_active', 'active_at'], 'integer'],
            [['condition', 'discount'], 'number'],
            [['name'], 'string', 'max' => 128],
            [['pt_id'], 'exist', 'skipOnError' => true, 'targetClass' => PromotionType::className(), 'targetAttribute' => ['pt_id' => 'id']],

            [['start_from','end_to'],'date','format'=>'yyyy-mm-dd'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pt_id' => '优惠类型',
            'limit' => '活动范围',
            'target_id' => '平台/商家/门店名称',
            'name' => '活动名称',
            'condition' => '条件(满XX元使用)',
            'discount' => '优惠金额',
            'valid_circle' => '有效期',
            'start_at' => '开始时间',
            'end_at' => '结束时间',
            'time' => '使用次数',
            'regist_at' => '发布时间',
            'is_active' => '是否上架',
            'active_at' => '上架更改时间',

            'start_from' => '开始时间',
            'end_to' => '结束时间',
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
                    $str=empty($query)?'':$query->name;
                    break;
                default: $str='活动适用对象有误'; break;
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



}
