<?php

namespace admin\models;

use api\models\GoodInfo;
use Yii;

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
            'gid' => '商品id',
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

}
