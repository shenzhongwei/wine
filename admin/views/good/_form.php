<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use admin\models\MerchantInfo;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use kartik\widgets\Select2;
use admin\models\GoodType;
use kartik\widgets\FileInput;
use yii\redactor\widgets\Redactor;
use ijackua\lepture\Markdowneditor;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodInfo $model
 * @var yii\widgets\ActiveForm $form
 */
$admin = Yii::$app->user->identity;
$merchants = MerchantInfo::GetMerchants();
$wa_type = $admin->wa_type;
$payArr = [
    1=>'余额',
    2=>'支付宝',
    3=>'微信'
];
?>
<style>
    .radio-inline input[type="radio"] {
        margin-top: 1px;
    }
</style>
<div class="good-info-form">
    <?php $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_VERTICAL,
        'fullSpan'=>12,
        'formConfig' => [
            'deviceSize' => ActiveForm::SIZE_LARGE,
        ],
    ]);
    ?>
<!--        <div class="panel panel-info">-->
<!--            <div class="panel-heading">-->
<!--                --><?//= $model->isNewRecord ? '发布商品' : '编辑商品' ?>
<!--            </div>-->
<!--            <div class="panel-body">-->

                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-6">
                            <?php
                            echo Form::widget([
                                'model' => $model,
                                'form' => $form,
                                'columns' => 1,
                                'columnSize'=>'lg',
                                'attributes' => [

                                    'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['maxlength'=>50,]],

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
                                            'data'=>GoodType::GetTypes(),
                                            'options'=>['placeholder'=>'请选择商品大类'],
                                            'pluginOptions' => ['allowClear' => true],
                                        ]
                                    ],

                                    'color'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                                        'options'=>[
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodColors'),
                                            'options'=>[ 'placeholder'=>'请选择颜色'],
                                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                            'pluginOptions'=>[
                                                'placeholder'=>'请选择颜色',
                                                'depends'=>['goodinfo-type'],
                                                'url' => Url::toRoute(['good/childs','key'=>'goodColors']),
                                            ]
                                        ],
                                    ],

                                    'dry'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                                        'options'=>[
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodDries'),
                                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                            'options'=>[ 'placeholder'=>'请选择干型'],
                                            'pluginOptions'=>[
                                                'placeholder'=>'请选择干型',
                                                'depends'=>['goodinfo-type'],
                                                'url' => Url::toRoute(['good/childs','key'=>'goodDries']),
                                            ]
                                        ],
                                    ],

                                    'boot'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                                        'options'=>[
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodBoots'),
                                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                            'options'=>[ 'placeholder'=>'请选择产地'],
                                            'pluginOptions'=>[
                                                'placeholder'=>'请选择产地',
                                                'depends'=>['goodinfo-type'],
                                                'url' => Url::toRoute(['good/childs','key'=>'goodBoots']),
                                            ]
                                        ],
                                    ],

                                    'breed'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                                        'options'=>[
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodBreeds'),
                                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                            'options'=>[ 'placeholder'=>'请选择品种'],
                                            'pluginOptions'=>[
                                                'placeholder'=>'请选择品种',
                                                'depends'=>['goodinfo-type'],
                                                'url' => Url::toRoute(['good/childs','key'=>'goodBreeds']),
                                            ]
                                        ],
                                    ],

                                    'country'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                                        'options'=>[
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodCountries'),
                                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                            'options'=>[ 'placeholder'=>'请选择国家'],
                                            'pluginOptions'=>[
                                                'placeholder'=>'请选择国家',
                                                'depends'=>['goodinfo-type'],
                                                'url' => Url::toRoute(['good/childs','key'=>'goodCountries']),
                                            ]
                                        ],
                                    ],

                                    'style'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                                        'options'=>[
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodStyles'),
                                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                            'options'=>[ 'placeholder'=>'请选择类型'],
                                            'pluginOptions'=>[
                                                'placeholder'=>'请选择类型',
                                                'depends'=>['goodinfo-type'],
                                                'url' => Url::toRoute(['good/childs','key'=>'goodStyles']),
                                            ]
                                        ],
                                    ],
                                    'vip_show'=>['type'=> Form::INPUT_RADIO_LIST,'items'=>['0'=>'不显示','1'=>'显示'],'options'=>['inline'=>true]],

                                    'point_sup'=>['type'=> Form::INPUT_RADIO_LIST,'items'=>['0'=>'不支持','1'=>'支持'],'options'=>['inline'=>true]],
                                ]

                            ]);

                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?php
                            echo Form::widget([
                                'model' => $model,
                                'form' => $form,
                                'columns' => 1,
                                'columnSize'=>'sm',
                                'attributes' => [

                                    'price'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'售价:单位：元', 'maxlength'=>10,'onkeyup'=>'clearNoNum(this)']],

                                    'pro_price'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'优惠价:单位：元', 'maxlength'=>10,'onkeyup'=>'clearNoNum(this)']],

                                    'cost'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'成本价:单位：元', 'maxlength'=>10,'onkeyup'=>'clearNoNum(this)']],

                                    'vip_price'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'会员价:单位：元', 'maxlength'=>10,'onkeyup'=>'clearNoNum(this)']],

                                    'brand'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                                        'options'=>[
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodBrands'),
                                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                            'options'=>['placeholder'=>'请选择品牌',],
                                            'pluginOptions'=>[
                                                'placeholder'=>'请选择品牌',
                                                'depends'=>['goodinfo-type'],
                                                'url' => Url::toRoute(['good/childs','key'=>'goodBrands']),
                                            ]
                                        ],
                                    ],

                                    'smell'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                                        'options'=>[
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodSmells'),
                                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                            'options'=>['placeholder'=>'请选择香型',],
                                            'pluginOptions'=>[
                                                'placeholder'=>'请选择香型',
                                                'depends'=>['goodinfo-type'],
                                                'url' => Url::toRoute(['good/childs','key'=>'goodSmells']),
                                            ]
                                        ],
                                    ],

                                    'vip_pay'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                                        'options'=>[
                                            'value' => $model->vip_pay, // initial value
                                            'data'=>$payArr,
                                            'options'=>['placeholder'=>'会员商品支持支付方式','multiple' => true],
                                            'pluginOptions' => [
                                                'disabled'=>true,
                                                'allowClear' => true,
                                                'tags' => true,
                                                'maximumInputLength' => 3
                                            ],
                                        ]
                                    ],

                                    'original_pay'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                                        'options'=>[
                                            'value' => $model->original_pay, // initial value
                                            'data'=>$payArr,
                                            'options'=>['placeholder'=>'普通商品支持支付方式','multiple' => true],
                                            'pluginOptions' => [
                                                'disabled'=>true,
                                                'allowClear' => true,
                                                'tags' => true,
                                                'maximumInputLength' => 3],
                                        ]
                                    ],
                                    'img'=>[
                                        'label'=>'图片（宽高比1:1）',
                                        'type'=> Form::INPUT_WIDGET, 'widgetClass'=>FileInput::className(),
                                        'options'=>[
                                            'options'=>[
                                                'accept'=>'image/*',
                                                'showUpload'=>false,
                                                'showRemove'=>false,
                                            ],
                                            'pluginOptions'=>[
                                                'initialPreview'=>empty($model->pic) ? false:[
                                                    "../../../photo".$model->pic,
                                                ],
                                                'uploadUrl' => Url::to(['/good/upload']),
                                                'uploadExtraData' => [
                                                    'id' => empty($model->id) ? 0:$model->id,
                                                ],
                                                'maxFileSize'=>1000,
                                                'previewFileType' => 'image',
                                                'initialPreviewAsData' => true,
                                                'showUpload'=>true,
                                                'showRemove'=>true,
                                                'autoReplace'=>true,
                                                'browseClass' => 'btn btn-success',
                                                'uploadClass' => 'btn btn-info',
                                                'removeClass' => 'btn btn-danger',
                                                'maxFileCount'=>1,
                                                'fileActionSettings' => [
                                                    'showZoom' => false,
                                                    'showUpload' => false,
                                                    'showRemove' => false,
                                                ],
                                            ],
                                            'pluginEvents'=>[
                                                'fileuploaderror'=>"function(object,data){
                                                 $('.fileinput-upload-button').attr('disabled',true);
                                                }",
                                                'fileerror'=>"function(object,data){
                                                 $('.fileinput-upload-button').attr('disabled',true);
                                                }",
                                                'fileclear'=>"function(){
                                                $('.fileinput-upload-button').attr('disabled',false);
                                    $('#goodinfo-pic').val('');
                                    }",
                                                'fileuploaded'  => "function (object,data){
			                    $('#goodinfo-pic').val(data.response.imageUrl);
		                    }",
                                                //错误的冗余机制
                                                'error' => "function (){
			                    alert('data.error');
		                    }"
                                            ]
                                        ]
                                    ],
                                ]

                            ]);
                            echo $form->field($model,'pic')->hiddenInput()->label(false);
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        echo Form::widget([
                            'model' => $model,
                            'form' => $form,
                            'columns' => 1,
                            'columnSize'=>'sm',
                            'attributes' => [
                                'detail'=>[
                                    'type'=> Form::INPUT_WIDGET, 'widgetClass'=>Redactor::className(),'options'=>[
                                        'clientOptions' => [
                                            'imageManagerJson' => ['/redactor/upload/image-json'],
                                            'imageUpload' => ['/redactor/upload/image'],
                                            'fileUpload' => ['/redactor/upload/file'],
                                            'minHeight' => '800px',
                                            'lang' => 'zh_cn',
                                            'plugins' => ['clips', 'fontcolor','imagemanager'],
                                        ]
                                    ],
                                ],
                            ]

                        ]);
                        ?>
                    </div>
                </div>
                <div class="row col-sm-4">
                    <?php
                    echo Html::submitButton('保存', ['class' => 'btn btn-success btn-block']);
                    ?>
                </div>
<!--            </div>-->
<!--        </div>-->
        <?php
        ActiveForm::end();
        ?>
</div>
<!--<script>-->
<!--    $(function () {-->
<!--        $('input[name="GoodInfo[point_sup]"').attr('disabled',true);-->
<!--    })-->
<!--</script>-->
