<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use admin\models\GoodPic;
use kartik\file\FileInput;
/**
 * @var yii\web\View $this
 * @var admin\models\GoodPic $model
 */
?>
<style>
    .select2-dropdown--below{
        z-index: 99999;
    }
</style>
    <div class="good-pic-form">

        <?php $form = ActiveForm::begin([
            'id' => 'pic-form',
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableAjaxValidation' => true, //开启ajax验证
            'validationUrl' => Url::toRoute(['valid-form', empty($model->id) ? '':'id'=>$model->id]), //验证url
            'action' => Url::toRoute([empty($model->id) ? 'good/pic-create':'good/pic-update',
                'key' => $model->gid,empty($model->id)?'':'id'=>$model->id]),
        ]);
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'gid' => ['type' => Form::INPUT_WIDGET, 'widgetClass'=>\kartik\select2\Select2::className(),'options'=>[
                    'data' => GoodPic::GetAllGoods(),
                    'options' => ['placeholder' => '请选择类型'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],],
                    ],
                'url'=>[
                    'label'=>'图片（宽高3:2）',
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
                            'uploadUrl' => Url::to(['/good/upload-pic']),
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
                            'fileuploaderror'=>"function(){
                                                 $('.fileinput-upload-button').attr('disabled',true);
                                                }",
                            'fileclear'=>"function(){
                                    $('#goodpic-pic').val('');
                                    }",
                            'fileuploaded'  => "function (object,data){
			                    $('#goodpic-pic').val(data.response.imageUrl);
		                    }",
                            //错误的冗余机制
                            'error' => "function (){
			                    alert('data.error');
		                    }"
                        ]
                    ]
                ],
                'pic'=>['type'=>Form::INPUT_HIDDEN,'label'=>false]
            ]
        ]);
        echo Html::submitButton('保存', [ 'class'=>'btn btn-success']);
        ActiveForm::end(); ?>

    </div>
