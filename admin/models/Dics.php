<?php

namespace admin\models;

use common\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "dics".
 *
 * @property string $type
 * @property integer $id
 * @property string $name
 */
class Dics extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dics';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type', 'name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => '类型',
            'id' => 'id',
            'name' => '名称',
        ];
    }

    /*
     * 获取所有的订单状态
     */
    public static function getAllorderstate(){
        $model=Dics::find()->select(['id','name'])->andWhere(['type'=>'订单状态'])->asArray()->all();
        $query=array();
        foreach($model as $k=>$v){
            $query[$v['id']]=$v['name'];
        }
        return $query;
    }


    /*
     * 获取优惠适用对象
     */
    public static function getPromotionRange($id){
        if(empty($id)){
            $model=Dics::find()->andWhere(['type'=>'优惠适用对象'])->all();
        }else {
            $type = PromotionType::findOne($id);
            if (!empty($type)) {
                if (in_array($type->group, [1, 4])) {
                    $model = Dics::find()->andWhere(['type' => '优惠适用对象'])->all();
                } else {
                    $model = [[
                        'id' => 1,
                        'name' => '平台通用',]
                    ];
                }
            }else{
                $model=[];
            }
        }
        return ArrayHelper::map($model,'id','name');
    }

    /*
     * 获取钱包类型
     */
    public static function getAccountType(){
        $model=Dics::find()->select(['id','name'])->andWhere(['type'=>'钱包类型'])->asArray()->all();
        return ArrayHelper::map($model,'id','name');
    }

    /*
     * 获取图片类型
     */
    public static function getPicType($type){
        $query=Dics::find()->select(['id','name'])->andWhere("type='图片类型'");
        if($type<>7){
            $query->andWhere("name<>'启动页'");
        }
        $query->orderBy(['id'=>SORT_ASC]);
        $model = $query->all();
        $res = ArrayHelper::map($model,'id','name');
        return $res;
    }

    /*
     * 获取图片位置
     */
    public static function getPicPos(){
        $model=Dics::find()->select(['id','name'])->andWhere(['type'=>'广告图位置'])->orderBy(['id'=>SORT_ASC])->all();
        $res = ArrayHelper::map($model,'id','name');
        return $res;
    }

    /*
     * 消息类型
     */
    public static function getMessageType(){
        $model=Dics::find()->select(['id','name'])->andWhere(['type'=>'消息类型'])->asArray()->all();
        $query=array();
        foreach($model as $k=>$v){
            $query[$v['id']]=$v['name'];
        }
        return $query;
    }

    /*
     * 消息跳转页面
     */
    public static function getMessageToUrl(){
        $model=Dics::find()->select(['id','name'])->andWhere(['type'=>'消息跳转页面'])->asArray()->all();
        $query=array();
        foreach($model as $k=>$v){
            $query[$v['id']]=$v['name'];
        }
        return $query;
    }


    public static function getPromotionClass(){
        $data = self::find()->where("type='促销类别' and id<>2")->orderBy(['id'=>SORT_ASC])->all();
        return ArrayHelper::map($data,'id','name');
    }

//    public static function getPromotion(){
//        $data = self::find()->where(['type'=>'促销类别'])->orderBy(['id'=>SORT_ASC])->all();
//        return ArrayHelper::map($data,'id','name');
//    }

    public static function getPromotionEnv($class=null){
        if(empty($class)){
            $data = self::find()->where("type='促销环境' and id<>3")->orderBy(['id'=>SORT_ASC])->all();
        }else{
            switch ($class){
                case 1:
                    $data = [
                        [
                            'id'=>1,
                            'name'=>'用户注册',
                        ],
                        [
                            'id'=>2,
                            'name'=>'推荐成功',
                        ],
                    ];
                    break;
                case 2:
                    $data = [
                        [
                            'id'=>3,
                            'name'=>'下单时',
                        ],
                    ];
                    break;
                case 3:
                    $data = [
                        [
                            'id'=>4,
                            'name'=>'下单成功',
                        ],
                        [
                            'id'=>5,
                            'name'=>'被推荐人下单成功',
                        ],
                    ];
                    break;
                case 4:
                    $data = [
                        [
                            'id'=>6,
                            'name'=>'充值成功',
                        ],
                    ];
                    break;
                default:
                    $data = [];
                    break;

            }
        }
        return ArrayHelper::map($data,'id','name');
    }

    public static function getPromotionGroup($class=null,$env=null){

        if(!empty($class)&&!empty($env)){
            if($class==1){
                if($env==1){
                    $data = [
                        [
                            'id'=>1,
                            'name'=>'赠送优惠券',
                        ],
                    ];
                }elseif ($env==2){
                    $data = [
                        [
                            'id'=>1,
                            'name'=>'赠送优惠券',
                        ],
                        [
                            'id'=>2,
                            'name'=>'赠送积分',
                        ],
                    ];
                }else{
                    $data = [];
                }
            }elseif ($class==2){
                $data = [
                    [
                        'id'=>4,
                        'name'=>'下单优惠',
                    ],
                ];
            }elseif ($class==3){
                if($env==4){
                    $data = [
//                            [
//                                'id'=>1,
//                                'name'=>'赠送优惠券',
//                            ],
//                            [
//                                'id'=>2,
//                                'name'=>'赠送积分',
//                            ],
                        [
                            'id'=>5,
                            'name'=>'分享网页',
                        ],
                    ];
                }elseif ($env==5){
                    $data = [
                        [
                            'id'=>1,
                            'name'=>'赠送优惠券',
                        ],
                        [
                            'id'=>2,
                            'name'=>'赠送积分',
                        ],
                    ];
                }else{
                    $data = [];
                }
            }else{
                $data = [
//                        [
//                            'id'=>1,
//                            'name'=>'赠送优惠券',
//                        ],
                    [
                        'id'=>2,
                        'name'=>'赠送积分',
                    ],
                    [
                        'id'=>3,
                        'name'=>'开通会员特权',
                    ],
                ];
            }
        }else{
            $data = self::find()->where("type='促销形式' and id<>4")->orderBy(['id'=>SORT_ASC])->all();
        }
        return ArrayHelper::map($data,'id','name');
    }

    public static function getPromotionLimit($class=null,$env=null,$group=null){
        if(!empty($class)&&!empty($env)&&!empty($group)){
            if($class==1||$class==3){
                $data = [
                    [
                        'id'=>1,
                        'name'=>'唯一',
                    ],
                ];
            }elseif ($class==2){
                $data = [
                    [
                        'id'=>2,
                        'name'=>'多个共存',
                    ],
                ];
            }else{
                if($group==2){
                    $data = [
                        [
                            'id'=>2,
                            'name'=>'多个共存',
                        ],
                    ];
                }else{
                    $data = [
                        [
                            'id'=>1,
                            'name'=>'唯一',
                        ],
                    ];
                }
            }
        }else{
            $data = self::find()->where(['type'=>'促销限制'])->orderBy(['id'=>SORT_ASC])->all();
        }
        return ArrayHelper::map($data,'id','name');
    }

    public static function GetPayModes(){
        return ArrayHelper::map(self::findAll(['type'=>'付款方式']),'id','name');
    }

    public static function GetOrderState(){
        return ArrayHelper::map(self::findAll(['type'=>'订单状态']),'id','name');
    }
}
