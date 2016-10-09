<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use kartik\file\FileInput;
use yii\helpers\Url;
use admin\models\GoodBrand;
/**
 * @var yii\web\View $this
 * @var admin\models\GoodBrand $model
 */
?>
<div class="good-brand-create">
    <div class="good-brand-form">

        <?php $form = ActiveForm::begin([
            'id'=>'brand-form',
            'type'=>ActiveForm::TYPE_VERTICAL,
            'action' => Url::toRoute(['type/child-create', 'key' => 'brand', 'type' => $model->type]),
            'enableAjaxValidation' => true, //开启ajax验证
            'validationUrl' => Url::toRoute(['valid-form', 'key' => 'brand']), //验证url
        ]);
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'请输入名称', 'maxlength'=>25]],
                'type'=>['type'=> Form::INPUT_DROPDOWN_LIST, 'items'=>GoodBrand::GetAllTypes(),'options'=>[
                    'readonly'=>true,
                    'options'=>['placeholder'=>'请选择类型'],
                ],],
            ]
        ]);
        echo $form->field($model,'url')->widget(FileInput::className(),[
            'options'=>[
                'accept'=>'image/*',
                'showUpload'=>false,
                'showRemove'=>false,
            ],
            'pluginOptions'=>[
//                            'showPreview' => false,
//                            'initialPreview'=>false,
                'uploadUrl' => Url::to(['/type/brand-upload']),
                'maxFileSize'=>1000,
                'previewFileType' => 'image',
//                            'initialPreviewAsData' => true,
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
                                    $('#goodbrand-logo').val('');
                                    }",
                'fileuploaded'  => "function (object,data){
			                    $('#goodbrand-logo').val(data.response.imageUrl);
		                    }",
                'error' => "function (){
			                    alert('data.error');
		                    }"
            ]
        ])->label('品牌logo（建议100*50）');
        echo $form->field($model,'logo')->hiddenInput()->label(false);
        echo Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
        ActiveForm::end(); ?>

    </div>
    </div>
