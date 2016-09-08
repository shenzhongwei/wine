<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use admin\models\MerchantInfo;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use kartik\widgets\Select2;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodInfo $model
 * @var yii\widgets\ActiveForm $form
 */
$admin = Yii::$app->user->identity;
$merchants = MerchantInfo::GetMerchants();
$wa_type = $admin->wa_type;
$types = \admin\models\GoodType::GetTypes();
?>

<div class="ibox-content good-info-form">

    <div class="col-sm-6">
    <?php $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_VERTICAL,
        'fullSpan'=>12,
        'formConfig' => [
            'deviceSize' => ActiveForm::SIZE_LARGE,
        ],
    ]);
    ?>
        <div class="row">
        <?php
    echo Form::widget([
        'model' => $model,
        'form' => $form,
        'columns' => 2,
        'columnSize'=>'sm',
        'attributes' => [

            'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['maxlength'=>50,]],

            'price'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'单价:单位：元', 'maxlength'=>10,'onkeyup'=>'this.value=this.value.replace(/\D/gi,"")']],

            'volum'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'容量：如：300ml', 'maxlength'=>128]],

            'unit'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'单位：如：瓶', 'maxlength'=>10]],

            'merchant'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                'options'=>[
                    'data'=>$merchants,
                    'options'=>['placeholder'=>'请选择商品所属商户', 'disabled'=>$wa_type>=2 ? true:false,],
                    'pluginOptions' => ['allowClear' => true],
                ]
            ],
            'type'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                'options'=>[
                    'data'=>$types,
                    'options'=>['placeholder'=>'请选择商品大类'],
                    'pluginOptions' => ['allowClear' => true],
                ]
            ],
            'brand'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=> [],
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'placeholder'=>'请选择品牌',
                        'depends'=>['goodinfo-type'],
                        'url' => Url::toRoute(['good/childs','key'=>'goodBrands']),
                        'loadingText' => 'Searching ...',
                    ]
                ],
            ],

            'smell'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=> [],
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'placeholder'=>'请选择香型',
                        'depends'=>['goodinfo-type'],
                        'url' => Url::toRoute(['good/childs','key'=>'goodSmells']),
                        'loadingText' => 'Searching ...',
                    ]
                ],
            ],

            'color'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=> [],
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'placeholder'=>'请选择颜色',
                        'depends'=>['goodinfo-type'],
                        'url' => Url::toRoute(['good/childs','key'=>'goodColors']),
                        'loadingText' => 'Searching ...',
                    ]
                ],
            ],

            'dry'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=> [],
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'placeholder'=>'请选择干型',
                        'depends'=>['goodinfo-type'],
                        'url' => Url::toRoute(['good/childs','key'=>'goodDries']),
                        'loadingText' => 'Searching ...',
                    ]
                ],
            ],

            'boot'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=> [],
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'placeholder'=>'请选择产地',
                        'depends'=>['goodinfo-type'],
                        'url' => Url::toRoute(['good/childs','key'=>'goodBoots']),
                        'loadingText' => 'Searching ...',
                    ]
                ],
            ],

            'breed'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=> [],
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'placeholder'=>'请选择品种',
                        'depends'=>['goodinfo-type'],
                        'url' => Url::toRoute(['good/childs','key'=>'goodBreeds']),
                        'loadingText' => 'Searching ...',
                    ]
                ],
            ],

            'country'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=> [],
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'placeholder' => '请选择国家',
                        'depends'=>['goodinfo-type'],
                        'url' => Url::toRoute(['good/childs','key'=>'goodCountries']),
                        'loadingText' => 'Searching ...',
                    ]
                ],
            ],

            'style'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=> [],
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'placeholder' => '请选择类型',
                        'depends'=>['goodinfo-type'],
                        'url' => Url::toRoute(['good/childs','key'=>'goodStyles']),
                        'loadingText' => 'Searching ...',
                    ]
                ],
            ],

            'pic'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'请上传一张产品图片', 'maxlength'=>128]],
        ]

    ]);

?>
        </div><div class="row">
            <?php
            echo Form::widget([
                'model' => $model,
                'form' => $form,
                'columns' => 1,
                'columnSize'=>'sm',
                'attributes' => [
                    'detail'=>[
                        'type'=> Form::INPUT_TEXTAREA, 'options'=>['placeholder'=>'详情...','rows'=> 6]
                    ],
                ]

            ]);
            ?>
        </div>
    <?php
    echo Html::submitButton($model->isNewRecord ? Yii::t('app', '保存') : Yii::t('app', '保存'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>
    </div>
</div>
