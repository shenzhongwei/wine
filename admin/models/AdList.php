<?php

namespace admin\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ad_list".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $target_id
 * @property integer $postion
 * @property string $pic
 * @property string $pic_url
 * @property integer $is_show
 */
class AdList extends \yii\db\ActiveRecord
{
    public $target_name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ad_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'target_id', 'is_show','postion'], 'integer'],
            [['type','pic',],'required'],
            [['pic', 'pic_url'], 'string', 'max' => 128],
            [['pic_url','type'],'validType',],
            [['target_name'],'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '广告类型',
            'target_id' => '对应的id',
            'postion'=>'广告位置',
            'pic' => '图片',
            'pic_url' => '图片链接',
            'is_show' => '是否显示',
            'target_name' => '对应类型的名称'
        ];
    }

    public function validType(){
        if(in_array($this->type,[1,7])&&empty($this->pic_url)){
            $this->addError('pic_url','请填写图片的外部链接地址');
        }
        if(in_array($this->type,[2,3,4,5,6])&&empty($this->target_id)){
            $this->addError('target_id','请选择对应的广告目标');
        }
        if($this->target_id!==null){
            $query = self::find()->where("type=$this->type and target_id=$this->target_id and pic_url='$this->pic_url' and is_show=1 and postion=$this->postion");
            if(!empty($this->id)){
                $query->andWhere("id<>$this->id");
            }
            $ad = $query->one();
            if(!empty($ad)){
                $this->addError('type','已存在该类型的显示中广告，请勿重复操作');
            }
        }

        if($this->type == 7){
            $bootQuery = self::find()->where("type=$this->type and is_show=1");
            if(!empty($this->id)){
                $bootQuery->andWhere("id<>$this->id");
            }
            $boot = $bootQuery->one();
            if(!empty($boot)){
                $this->addError('pic','已存在一张显示中的启动图，请勿重复操作');
            }
        }else{
            $adQuery = self::find()->where("is_show=1 and postion=$this->postion");
            if(!empty($this->id)){
                $adQuery->andWhere("id<>$this->id");
            }
            $count = $adQuery->count();
            if($count>=5){
                $this->addError('pic','该位置已存在5张显示中的广告图，请勿重复操作');
            }
        }
    }


    /*
     * 查询条件时，根据类型显示对应类型下的所有名称
     */
    public static function getacceptName($type){
        switch($type){
            case 1:
                $model=array(
                    array(
                        "id"=>"0",
                        "name"=>"外部网页",
                    )
                );
                break;
            case 2:  //商品广告
                $model =GoodInfo::find()->select(['id','name'])->asArray()->all();
                break;
            case 3:  //品牌广告
                $model=GoodBrand::find()->select(['id','name'])->asArray()->all();
                break;
            case 4: //商家广告
                $model=MerchantInfo::find()->select(['id','name'])->asArray()->all();
                break;
            case 5: //香型广告
                $model =GoodSmell::find()->select(['id','name'])->asArray()->all();
                break;
            case 6: //类型广告
                $model =GoodBreed::find()->select(['id','name'])->asArray()->all();
                break;
            case 7:
                $model=array(
                    array(
                        "id"=>"0",
                        "name"=>"启动页",
                    )
                );
                break;
            case 8:
                $model=array(
                    array(
                        "id"=>"0",
                        "name"=>"充值页",
                    )
                );
                break;
            default:
                $model=[[]];
                break;
        }
        $results=[];
        if(!empty($model)){
            $results = ArrayHelper::map($model,'id','name');
        }
        return $results;
    }

    /*
     * 根据target_id获取name
     */
    public static function getOneName($model){
        switch($model->type){
            case 1:
                $name='外部网页';

                break;
            case 2:  //商品广告
                $query=GoodInfo::find()->where(['id'=>$model->target_id])->one();
                $name=empty($query)?'丢失':$query->name;

                break;
            case 3:  //品牌广告
                $query=GoodBrand::find()->where(['id'=>$model->target_id])->one();
                $name=empty($query)?'丢失':$query->name;

                break;
            case 4: //商家广告
                $query=MerchantInfo::find()->where(['id'=>$model->target_id])->one();
                $name=empty($query)?'丢失':$query->name;

                break;
            case 5: //香型广告
                $query=GoodSmell::find()->where(['id'=>$model->target_id])->one();
                $name=empty($query)?'丢失':$query->name;

                break;
            case 6: //类型广告
                $query=GoodType::find()->where(['id'=>$model->target_id])->one();
                $name=empty($query)?'丢失':$query->name;

                break;
            case 8:
                $name='充值';

                break;
            default:
                $name='无';
                break;
        }
        return $name;
    }

    /**
     * @return string
     * 获取target_id对应值
     */
    public static function GetChilds($type){
        switch($type){
            case 1:
                $name=[0=>'外部网页'];

                break;
            case 2:  //商品广告
                $query=GoodInfo::find()->all();
                $name=ArrayHelper::map($query,'id','name');

                break;
            case 3:  //品牌广告
                $query=GoodBrand::find()->all();
                $name=ArrayHelper::map($query,'id','name');

                break;
            case 4: //商家广告
                $query=MerchantInfo::find()->all();
                $name=ArrayHelper::map($query,'id','name');

                break;
            case 5: //香型广告

                $query=GoodSmell::find()->all();
                $name=ArrayHelper::map($query,'id','name');

                break;
            case 6: //类型广告
                $query=GoodType::find()->all();
                $name=ArrayHelper::map($query,'id','name');

                break;
            case 8:
                $name=[0=>'充值'];

                break;
            default:
                $name=[0=>'无'];
                break;
        }
        return $name;
    }

    /*
     * 图片随机命名
     */
    public static function generateCode(){
        $arr = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
        $key = array_rand($arr,3); //array_rand() 函数返回数组中的随机键
        $code='';
        foreach($key as $k=>$v){
            $code.=$arr[$v];
        }

        return $code;
    }
}
