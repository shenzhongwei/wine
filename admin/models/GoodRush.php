<?php

namespace admin\models;

use common\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "good_rush".
 *
 * @property integer $id
 * @property integer $gid
 * @property string $price
 * @property string $rush_pay
 * @property integer $limit
 * @property integer $amount
 * @property string $start_at
 * @property string $end_at
 * @property integer $point_sup
 * @property integer $is_active
 *
 * @property GoodInfo $g
 */
class GoodRush extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_rush';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gid','price','limit','start_at','end_at','amount','rush_pay','point_sup'], 'required','on'=>['add','update']],
            [['gid', 'limit', 'amount', 'is_active','point_sup'], 'integer'],
            [['price'], 'number'],
            [['rush_pay'], 'string', 'max' => 10],
            [['price','amount','limit'],'compare','compareValue'=>0,'operator'=>'>'],
            ['limit','validPrice','on'=>['add','update']],
            [['start_at', 'end_at'], 'safe'],
            [['gid'], 'exist', 'skipOnError' => true, 'targetClass' => GoodInfo::className(), 'targetAttribute' => ['gid' => 'id']],
            ['gid','validGood','on'=>['add','update']],
            ['price','validPrice','on'=>['add','update']],
            ['end_at', 'validTime','on'=>['add','update']],
        ];
    }

    public function scenarios()
    {
        $behavior = parent::scenarios();
        $behavior['add']=['id','gid','price','is_active','limit','start_at','end_at','amount','point_sup','rush_pay'];
        $behavior['update']=['id','gid','price','is_active','limit','start_at','end_at','amount','point_sup','rush_pay'];
        return $behavior;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'gid' => '商品',
            'price' => '抢购价',
            'limit' => '单号限购数量',
            'point_sup'=>'积分支持',
            'rush_pay'=>'抢购支付方式',
            'amount' => '抢购库存',
            'start_at' => '开始时间',
            'end_at' => '结束时间',
            'is_active' => '是否上架',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getG()
    {
        return $this->hasOne(GoodInfo::className(), ['id' => 'gid'])->where('good_info.id>0');
    }

    public static function GetGoodNames(){
        $query = GoodInfo::find()->joinWith('goodRushes')->where('good_rush.id>0');
        $admin = Yii::$app->user->identity;
        $adminType = $admin->wa_type;
        $adminId = $admin->wa_id;
        if($adminType>2){
            $manager = MerchantInfo::findOne(['wa_id'=>$adminId]);
            $query->andWhere(['merchant'=>empty($manager) ? 0:$manager->id]);
        }
        $goods = $query->all();
        if(empty($goods)){
            return [];
        }
        return array_values(array_unique(ArrayHelper::getColumn($goods,'name')));
    }

    /**
     * 验证规则
     */
    public function validGood(){
        if(!empty($this->start_at)&&!empty($this->end_at)){
            $query = self::find()->where("gid=$this->gid and (start_at<'$this->end_at' or end_at>'$this->start_at')");
            if(!empty($this->id)){
                $query->andWhere("id <> $this->id");
            }
            $good = $query->one();
            if(!empty($good)){
                return $this->addError('gid','该时间段内已存在该产品的抢购');
            }
        }
    }

    public function validPrice(){
        $good = GoodInfo::findOne($this->gid);
        if($this->price>=$good->pro_price){
            $this->addError('price','抢购价不得高于售价');
        }elseif ($this->limit>$this->amount){
            $this->addError('limit','单号限购数不能大于库存');
        }
    }

    public function validTime(){
        if(!empty($this->end_at)&&!empty($this->start_at)&&$this->end_at<=$this->start_at){
            return $this->addError('end_at','结束时间必须大于开始时间');
        }
    }



}
