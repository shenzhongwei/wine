<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use kartik\file\FileInput;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodType $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="good-type-form" style="width: 80%">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_VERTICAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

//            'regist_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 添加时间...']],

//            'is_active'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 是否上架...']],

//            'active_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 上架状态更改时间...']],

            'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'请输入名称', 'maxlength'=>25]],
            'url'=>[
                'label'=>'大类logo（建议50*50）',
                'type'=> Form::INPUT_WIDGET, 'widgetClass'=>FileInput::className(),
                'options'=>[
                    'options'=>[
                        'accept'=>'image/*',
                        'showUpload'=>false,
                        'showRemove'=>false,
                    ],
                    'pluginOptions'=>[
                        'initialPreview'=>empty($model->logo) ? false:[
                            "../../../../photo".$model->logo,
                        ],
                        'uploadUrl' => Url::to(['/type/upload']),
                        'maxFileSize'=>200,
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
                                    $('#goodtype-logo').val('');
                                    }",
                        'fileuploaded'  => "function (object,data){
			                    $('#goodtype-logo').val(data.response.imageUrl);
		                    }",
                        //错误的冗余机制
                        'error' => "function (){
			                    alert('data.error');
		                    }"
                    ]
                ]
            ],
            'logo'=>['type'=> Form::INPUT_HIDDEN, 'options'=>['placeholder'=>'', 'maxlength'=>255],'label'=>false],

        ]

    ]);

    echo Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
