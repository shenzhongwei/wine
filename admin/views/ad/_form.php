<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\AdList $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="ad-list-form">

    <?php
       $form = ActiveForm::begin([
           'type'=>ActiveForm::TYPE_HORIZONTAL,
           'options' => ['enctype' => 'multipart/form-data']
       ]);
    ?>
    <div class="ad-form">

        <?= $form->field($model, 'type')->dropDownList(\admin\models\Dics::getPicType(),['prompt'=>'请选择广告类型',$model->isNewRecord?'':'disabled'=>true])->hint('注：启动页只能有一张')?>

        <?= $form->field($model, 'target_id')->widget(\kartik\widgets\DepDrop::className(),[
            'data'=> $model->type==='' ? [] : \admin\models\AdList::getacceptName($model->type),
            'options'=>[ 'placeholder'=>'请选择对应类型的名称'],
            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
            'pluginOptions'=>[
                'placeholder'=>'请选择对应类型的名称',
                'depends'=>['adlist-type'],
                'url' =>\yii\helpers\Url::to(['ad/relation-name']),
                'loadingText' => '查找...',
            ]
        ])->label('对应类型的名称')?>

        <input type="hidden" value="<?=$model->pic?>" name="AdList[pic_url]">
        <?= $form->field($model,'pic')->widget(\kartik\file\FileInput::className(),[
            'options'=>[
                'accept'=>'image/*',
            ],
            'pluginOptions'=>[
                'previewFileType' => 'image',
                'initialPreview' =>$p1,
                'initialPreviewConfig' =>$P,
                'initialPreviewAsData' => true,
                'showUpload'=>false,
                'showRemove'=>false,
                'autoReplace'=>true,
                'maxFileCount'=>1,
            ]
        ])?>
        <?= $form->field($model, 'url')->textInput(['maxlength' =>128])?>
    </div>

    <p style="margin: 0 auto;text-align: center;margin-bottom: 2px;">
        <?=Html::submitButton($model->isNewRecord ? Yii::t('app', '创建') : Yii::t('app', '更新'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);?>
    <?=Html::a('返回', 'javascript:history.go(-1);location.reload();', ['class' => 'btn btn-primary','style'=>'margin-left:10px']);?>
    </p>
    <?php ActiveForm::end(); ?>

</div>
