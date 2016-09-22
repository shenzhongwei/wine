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
?>
<div class="good-info-form">

    <div class="col-sm-12">
    <?php $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_VERTICAL,
        'fullSpan'=>12,
        'formConfig' => [
            'deviceSize' => ActiveForm::SIZE_LARGE,
        ],
    ]);
    ?>
        <div class="panel panel-info">
            <div class="panel-heading">
                <?= $model->isNewRecord ? '发布商品' : '编辑商品' ?>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-6">
                            <?php
                            echo Form::widget([
                                'model' => $model,
                                'form' => $form,
                                'columns' => 1,
                                'columnSize'=>'sm',
                                'attributes' => [

                                    'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['maxlength'=>50,]],

                                    'volum'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'容量：如：300ml', 'maxlength'=>128]],

                                    'merchant'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                                        'options'=>[
                                            'data'=>$merchants,
                                            'options'=>['placeholder'=>'请选择商品所属商户', 'disabled'=>$wa_type>=2 ? true:false,],
                                            'pluginOptions' => ['allowClear' => true],
                                        ]
                                    ],

                                    'brand'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                                        'options'=>[
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodBrands'),
                                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                            'pluginOptions'=>[
                                                'placeholder'=>'请选择品牌',
                                                'depends'=>['goodinfo-type'],
                                                'url' => Url::toRoute(['good/childs','key'=>'goodBrands']),
                                                'loadingText' => 'Searching ...',
                                            ]
                                        ],
                                    ],

                                    'color'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                                        'options'=>[
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodColors'),
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
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodDries'),
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
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodBoots'),
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
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodBreeds'),
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
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodCountries'),
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
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodStyles'),
                                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                            'pluginOptions'=>[
                                                'placeholder' => '请选择类型',
                                                'depends'=>['goodinfo-type'],
                                                'url' => Url::toRoute(['good/childs','key'=>'goodStyles']),
                                                'loadingText' => 'Searching ...',
                                            ]
                                        ],
                                    ],
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

                                    'price'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'单价:单位：元', 'maxlength'=>10,'onkeyup'=>'this.value=this.value.replace(/\D/gi,"")']],

                                    'unit'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'单位：如：瓶', 'maxlength'=>10]],

                                    'type'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                                        'options'=>[
                                            'data'=>GoodType::GetTypes(),
                                            'options'=>['placeholder'=>'请选择商品大类'],
                                            'pluginOptions' => ['allowClear' => true],
                                        ]
                                    ],

                                    'smell'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                                        'options'=>[
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'data'=> empty($model->id) ? []:GoodType::GetChilds($model->type,'goodSmells'),
                                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                            'pluginOptions'=>[
                                                'placeholder'=>'请选择香型',
                                                'depends'=>['goodinfo-type'],
                                                'url' => Url::toRoute(['good/childs','key'=>'goodSmells']),
                                                'loadingText' => 'Searching ...',
                                            ]
                                        ],
                                    ],

                                    'img'=>[
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
                                                'fileclear'=>"function(){
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
                                            'minHeight' => '500px',
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
                    echo Html::submitButton($model->isNewRecord ? Yii::t('app', '保存') : Yii::t('app', '保存'), ['class' => 'btn btn-primary btn-rounded btn-block']);
                    ?>
                </div>
            </div>
        </div>
        <?php
        ActiveForm::end();
        ?>
    </div>
</div>
