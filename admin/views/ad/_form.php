<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use kartik\select2\Select2;
use admin\models\Dics;
use kartik\file\FileInput;
use kartik\depdrop\DepDrop;
use admin\models\AdList;
/**
 * @var yii\web\View $this
 * @var admin\models\AdList $model
 * @var yii\widgets\ActiveForm $form
 */
//var_dump($model->postion);
//exit;
?>
<style>
    .select2-dropdown--below{
        z-index: 99999;
    }
</style>
    <div class="ad-list-form">

        <?php $form = ActiveForm::begin([
            'type'=>ActiveForm::TYPE_VERTICAL,
            'id'=>'ad-form',
            'formConfig' => [
                'deviceSize' => ActiveForm::SIZE_SMALL,
            ],
            'enableAjaxValidation'=>true, //开启ajax验证
            'validationUrl'=>Url::toRoute(['valid-form','id'=>empty($model->id)?0:$model->id, 'key' => $model->type==7 ? 'boot':($model->postion==1 ? 'head':'middle')]), //验证url
            'action' => Url::toRoute([empty($model['id']) ? 'ad/create':'ad/update',
                'key' => $model->type==7 ? 'boot':($model->postion==1 ? 'head':'middle'),
                empty($model['id']) ? '':'id'=>$model->id
            ]),
        ]);
        ?>

        <?php

        echo Form::widget([

            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [

                'id'=>['type'=>Form::INPUT_HIDDEN,'label'=>false],

                'type'=>['label'=>'广告类型','type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                    'options'=>[
                        'value' => $model->type, // initial value
                        'data'=>Dics::getPicType($model->type),
                        'options'=>['placeholder'=>'选择广告类型','disabled'=>$model->type==7 ? true:false],
                        'pluginOptions' => [
                            'disabled'=>$model->type==7 ? true:false,
                            'allowClear' => true,
                        ],
                    ]
                ],

                'target_id'=>[
                    'type'=> in_array($model->type,[7]) ? Form::INPUT_HIDDEN:Form::INPUT_WIDGET,
                    'label'=>in_array($model->type,[7]) ? false:'广告对象',
                    in_array($model->type,[7]) ? '':'widgetClass'=>DepDrop::className(),
                    'options'=>in_array($model->type,[7]) ? []:[
                        'type' => DepDrop::TYPE_SELECT2,
                        'data'=> empty($model->id) ? []:AdList::GetChilds($model->type),
                        'options'=>[ 'placeholder'=>'广告对象','disabled'=>in_array($model->type,[1,8]) ? true:false,],
                        'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                        'pluginOptions'=>[
                            'placeholder'=>'请选择广告对象',
                            'depends'=>['adlist-type'],
                            'url' => Url::toRoute(['ad/relation-name']),
                        ],

                        'pluginEvents' => [
                            "depdrop.afterChange"=>"function(event, id, value) { 
                            if(value==1){
                            $(this).val(0);
                             $(this).attr('disabled',true);
                            $('#adlist-pic_url').attr('disabled',false);
                             $('#adlist-pic_url').attr('placeholder','填写图片外部链接');
                            }else if(value==8){
                            $(this).val(0);
                             $(this).attr('disabled',true);
                             $('#adlist-pic_url').val('');
                             $('#adlist-pic_url').attr('placeholder','该类型无需填写外部链接');
                             $('#adlist-pic_url').attr('disabled',true);
                            }else{
                            $(this).val(0);
                            $('#adlist-pic_url').val('');
                            $('#adlist-pic_url').attr('placeholder','该类型无需填写外部链接');
                            $('#adlist-pic_url').attr('disabled',true);
                            }
                             }",
                        ],
                    ],
                ],

                'postion'=>[
                    'type'=> in_array($model->type,[7]) ? Form::INPUT_HIDDEN : Form::INPUT_WIDGET,
                    'widgetClass'=>Select2::className(),
                    'label'=>in_array($model->type,[7]) ? false:'广告位置',
                    'options'=>in_array($model->type,[7]) ? ['placeholder'=>'']:[
                        'value' => $model->postion, // initial value
                        'data'=>Dics::getPicPos(),
                        'options'=>['placeholder'=>'选择广告位置','disabled'=>true],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ],
                'url'=>[
                    'label'=>'广告图片（建议180*100）',
                    'type'=> Form::INPUT_WIDGET,
                    'widgetClass'=>FileInput::className(),
                    'options'=>[
                        'options'=>[
                            'accept'=>'image/*',
                            'showUpload'=>false,
                            'showRemove'=>false,
                        ],
                        'pluginOptions'=>[
                            'initialPreview'=>empty($model->pic) ? false:[
                                '../../../photo'.$model->pic,
                            ],
                            'uploadUrl' => Url::to(['/ad/upload']),
                            'uploadExtraData' => [
                                'key' => $model->type==7 ? 'boot':($model->postion==1 ? 'head':'middle'),
                            ],
                            'maxFileSize'=>500,
                            'previewFileType' => 'image',
                            'initialPreviewAsData' => true,
                            'width'=>'50px',
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
                            'fileloaded'=>"function(){
                                $('.file-preview-image').removeAttr('style');;
                                $('.file-preview-image').css('max-width','400px');
                                $('.file-preview-image').css('max-height','200px');
                            }",
                            'fileuploaderror'=>"function(){
                                $('.fileinput-upload-button').attr('disabled',true);
                            }",
                            'fileclear'=>"function(){
                                $('#adlist-pic').val('');
                            }",
                            'fileuploaded'  => "function (object,data){
                                $('#adlist-pic').val(data.response.imageUrl);
		                    }",
                            //错误的冗余机制
                            'error' => "function (){
			                    alert('data.error');
		                    }"
                        ]
                    ]
                ],
                'pic'=>['type'=> Form::INPUT_HIDDEN, 'options'=>['placeholder'=>'', 'maxlength'=>255],'label'=>false],
                'pic_url'=>[
                    'type'=> Form::INPUT_TEXT,
                    'label'=>'外部链接网址',
                    'options'=>['placeholder'=>in_array($model->type,[2,3,4,5,6,8]) ?
                        '该类型无需填写外部链接':'填写图片外部链接',
                        'disabled'=>in_array($model->type,[2,3,4,5,6,8]) ? true:false]
                ],
            ]

        ]);

        echo Html::submitButton(Yii::t('app', 'Save') , ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
        ?>

        <?php
        ActiveForm::end(); ?>
    </div>
<script language="JavaScript">
    $(function () {
        $('.file-preview-image').removeAttr("style");
        $('.file-preview-image').css('max-width','400px');
        $('.file-preview-image').css('max-height','200px');
    });
</script>
