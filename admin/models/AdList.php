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
 * @property string $pic
 * @property string $url
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
            [['type', 'target_id', 'is_show'], 'integer'],
            [['pic', 'url'], 'string', 'max' => 128],

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
            'pic' => '图片',
            'url' => '图片链接',
            'is_show' => '是否显示',

            'target_name' => '对应类型的名称'
        ];
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
                $query=\admin\models\GoodInfo::find()->where(['id'=>$model->target_id])->one();
                $name=empty($query)?'':$query->name;

                break;
            case 3:  //品牌广告
                $query=\admin\models\GoodBrand::find()->where(['id'=>$model->target_id])->one();
                $name=empty($query)?'':$query->name;

                break;
            case 4: //商家广告
                $query=\admin\models\MerchantInfo::find()->where(['id'=>$model->target_id])->one();
                $name=empty($query)?'':$query->name;

                break;
            case 5: //香型广告
                $query=\admin\models\GoodSmell::find()->where(['id'=>$model->target_id])->one();
                $name=empty($query)?'':$query->name;

                break;
            case 6: //类型广告
                $query=\admin\models\GoodBreed::find()->where(['id'=>$data->target_id])->one();
                $name=empty($query)?'':$query->name;

                break;
            case 7:
                $name='启动页';

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
