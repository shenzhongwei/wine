<?php

namespace admin\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "good_vip".
 *
 * @property integer $id
 * @property integer $gid
 * @property string $price
 * @property integer $limit
 * @property integer $is_active
 *
 * @property GoodInfo $g
 */
class GoodVip extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_vip';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gid', 'limit', 'is_active'], 'integer'],
            [['gid','price'],'required','on'=>['add','update']],
            [['gid'],'unique','on'=>'add','message'=>'该会员产品的已存在'],
            [['gid'],'validGid','on'=>'update','message'=>'该会员产品的已存在'],
            [['price'],'validPrice','on'=>['update','create']],
            [['price'], 'number'],
            [['gid'], 'exist', 'skipOnError' => true, 'targetClass' => GoodInfo::className(), 'targetAttribute' => ['gid' => 'id']],
        ];
    }

    public function scenarios()
    {
        $behavior = parent::scenarios();
        $behavior['add']=['id','gid','price','is_active'];
        $behavior['update']=['id','gid','price','is_active'];
        return $behavior;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'gid' => '商品',
            'price' => '会员价',
            'limit' => '限购数量',
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

    public function validGid(){
        $vip = self::find()->where('id<>'.$this->id.' and gid='.$this->gid)->one();
        if(!empty($vip)){
            return $this->addError('gid','该产品已参与会员活动');
        }
    }

    public function validPrice(){
        $good = GoodInfo::findOne($this->gid);
        if($this->price>=$good->pro_price){
            $this->addError('price','会员价不得高于售价');
        }
    }

    public static function GetGoods(){
        $query = GoodInfo::find()->where(['is_active'=>1]);
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
        return ArrayHelper::map(ArrayHelper::getColumn($goods,function($element){
            return [
                'id'=>$element['id'],
                'name'=>$element['name'].$element['volum'].' （销售价：¥'.$element['pro_price'].'）',
            ];
        }),'id','name');
    }


    public static function GetGoodNames(){
        $admin = Yii::$app->user->identity;
        $adminType = $admin->wa_type;
        $adminId = $admin->wa_id;
        $query = GoodInfo::find()->joinWith('goodVips')->where('good_vip.id>0');
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

}
