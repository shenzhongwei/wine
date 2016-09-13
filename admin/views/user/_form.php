<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\widgets\DatePicker;

/**
 * @var yii\web\View $this
 * @var admin\models\UserInfo $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="user-info-form">

    <?php $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_HORIZONTAL,
        'options' => ['enctype' => 'multipart/form-data']
    ]);?>
    <div class="user-form" style="margin-top: 10px;width: 90%;">

        <?=$form->field($model,'phone')->textInput(['placeholder'=>'输入手机号', 'maxlength'=>13,$model->isNewRecord?'':'readonly'=>true]);?>

        <?=$form->field($model,'sex')->dropDownList(['保密'=>'保密','男'=>'男','女'=>'女'],['options'=>['placeholder'=>'请选择性别']]);?>

        <?=$form->field($model,'is_vip')->dropDownList(['0'=>'否','1'=>'是']);?>

        <input type="hidden" name="UserInfo[header]" value="<?=$model->head_url?>">
        <?=$form->field($model,'head_url')->widget(\kartik\file\FileInput::className(),[
              'options'=>[
                  'accept'=>'image/*',
              ],
              'pluginOptions'=>[
                  'previewFileType' => 'image',
                  'initialPreview' =>$p1,
                  'initialPreviewConfig' =>$PreviewConfig,
                  'initialPreviewAsData' => true,
                  'showUpload'=>false,
                  'showRemove'=>false,
                  'autoReplace'=>true,
                  'maxFileCount'=>1,
              ]
          ]);?>

        <?=$form->field($model,'birth')->widget(DatePicker::className(),[
              'options' => ['placeholder' => '',],
              'pluginOptions' => [
                  'todayHighlight' => true,
                  'format' => 'yyyy-mm-dd',
              ]
          ]);?>

        <?=$form->field($model,'nickname')->textInput(['maxlength'=>32]);?>

        <?=$form->field($model,'realname')->textInput(['maxlength'=>32,$model->isNewRecord?'':'readonly'=>true]);?>

        <?=$form->field($model,'invite_user')->textInput(['maxlength'=>32,$model->isNewRecord?'':'readonly'=>true])->label('邀请人');?>

        <?=$form->field($model,'invite_code')->textInput(['maxlength'=>32,$model->isNewRecord?'':'readonly'=>true]);?>
    </div>
    <p style="margin: 0 auto;text-align: center;margin-bottom: 2px;">
        <?=Html::submitButton($model->isNewRecord ? Yii::t('app', '创建') : Yii::t('app', '更新'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);?>
        <?=Html::a('返回', 'javascript:history.go(-1);location.reload()', ['class' => 'btn btn-primary','style'=>'margin-left:10px']);?>
    </p>
    <?php ActiveForm::end(); ?>
</div>
