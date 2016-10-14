<?php

namespace admin\models;

use common\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "good_pic".
 *
 * @property integer $id
 * @property integer $gid
 * @property string $pic
 * @property integer $status
 *
 * @property GoodInfo $g
 */
class GoodPic extends \yii\db\ActiveRecord
{
    public $url;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_pic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gid', 'status'], 'integer'],
            [['pic'], 'string', 'max' => 250],
            [['gid','pic'],'required'],
            ['gid','ValidGid'],
            [['gid'], 'exist', 'skipOnError' => true, 'targetClass' => GoodInfo::className(), 'targetAttribute' => ['gid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'gid' => '产品id',
            'pic' => '图片地址',
            'status' => '状态',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getG()
    {
        return $this->hasOne(GoodInfo::className(), ['id' => 'gid']);
    }

    public function ValidGid(){
        $query = self::find()->where(['status'=>1,'gid'=>$this->gid]);
        if(!empty($this->id)){
            $query->andWhere("id<>$this->id");
        }
        $count = $query->count();
        if($count>=4){
            $this->addError('该产品的轮播图已达上限');
        }
    }

    public static function GetAllGoods(){
        $goods = GoodInfo::find()->addSelect(['id','concat(name,volum) as good_name'])->where(['is_active'=>1])->asArray()->all();
        return ArrayHelper::map($goods,'id','good_name');
    }

}
