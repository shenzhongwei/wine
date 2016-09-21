<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use yii\helpers\ArrayHelper;
use admin\models\MerchantInfo;
use admin\models\Zone;
use yii\helpers\Url;
use kartik\file\FileInput;
/**
 * @var yii\web\View $this
 * @var admin\models\ShopInfo $model
 * @var yii\widgets\ActiveForm $form
 */
$merchant=ArrayHelper::map(MerchantInfo::find()->asArray()->all(),'id','name'); //第2个参数是value,第3个参数是option的值
//省
//$province=ArrayHelper::map(Zone::getProvince(),'id','name');
//$city=[];
//$district=[];
?>
<style>
    .shopinfo-no_send_need{width: 50px;height: 30px;;padding-left:2px ;padding-right: 2px; border: 1px solid #ccc;}
</style>
<script type="text/javascript">
    $(document).ready(function() {
        selectAddress($("#shopinfo-province"),$("#shopinfo-city"),$("#shopinfo-district"));
    });
</script>
<script type="text/javascript" src="<?=Url::to('@web/js/wine/address.js') ?>"></script>

<div class="shop-info-form">

    <?php
    $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation'=>true, //开启ajax验证
        'validationUrl'=>Url::toRoute(['valid-form','id'=>empty($model['id'])?0:$model['id']]), //验证url
        'options' => ['enctype' => 'multipart/form-data']
    ]);
    ?>
    <div class="merchant-form" style="margin-top: 10px;width: 90%;">
        <?= $form->field($model, 'merchant')->dropDownList($merchant,[$model->isNewRecord?'':'disabled'=>true])?>
        <?= $form->field($model, 'name')->textInput()?>
        <?= $form->field($model, 'limit')->textInput()?>
        <?= $form->field($model, 'least_money')->textInput()?>
        <?= $form->field($model, 'send_bill')->textInput()?>
        <?= $form->field($model, 'no_send_need',[
            'template'=> '<label class="control-label col-md-2" for="shopinfo-no_send_need" >免配送条件</label>
                        <div class="col-sm-10" >
                        <p>满<input type="text" id="shopinfo-no_send_need" name="ShopInfo[no_send_need]" class="shopinfo-no_send_need" placeholder="0">元，免配送费</p>
                        <div class="help-block"></div>
                        </div>'
        ])->textInput(['maxlength'=>20,'style'=>'width:50px'])?>

        <input type="hidden" value="<?=$model->bus_pic?>" name="ShopInfo[bus_pic_url]">
        <?= $form->field($model, 'bus_pic')->widget(FileInput::className(),[
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
        ])?>
        <input type="hidden" value="<?=$model->bus_pic?>" name="ShopInfo[logo_url]">
        <?= $form->field($model, 'logo')->widget(FileInput::className(),[
            'options'=>[
                'accept'=>'image/*',
            ],
            'pluginOptions'=>[
                'previewFileType' => 'image',
                'initialPreview' =>$p2,
                'initialPreviewConfig' =>$PreviewConfig,
                'initialPreviewAsData' => true,
                'showUpload'=>false,
                'showRemove'=>false,
                'autoReplace'=>true,
                'maxFileCount'=>1,
            ]
        ])?>
        <?= $form->field($model, 'phone')->textInput()?>
        <?= $form->field($model, 'province')->dropDownList($province,['prompt'=>'--省--'])?>
        <?= $form->field($model, 'city')->dropDownList($city,['prompt'=>'--市--',!$model->isNewRecord?'':'disabled'=>true])?>
        <?= $form->field($model, 'district')->dropDownList($district,['prompt'=>'--区--',!$model->isNewRecord?'':'disabled'=>true])?>
        <?= $form->field($model, 'region')->textInput(['maxlength' =>50])->label('小区名称') ?>
        <?= $form->field($model, 'address')->textInput(['maxlength' =>128])->label('门牌号') ?>
        <hr>
        <?php if($model->isNewRecord){ //创建时显示?>
        <?= $form->field($model, 'wa_username')->textInput(['maxlength' =>50])?>
        <?= $form->field($model, 'wa_password')->textInput(['maxlength' =>50])?>
        <?= $form->field($model, 'wa_type')->dropDownList($item_arr,['disabled'=>true]) ?>
        <input type="hidden" id="appliance-logoUrl" class="form-control"  name="MerchantInfo[logoUrl]" value="<?=$model->wa_logo?>">
        <?= $form->field($model, 'wa_logo')->widget(\kartik\file\FileInput::className(),[
            'options'=>[
                'accept'=>'image/*',
            ],
            'pluginOptions'=>[
                'previewFileType' => 'image',
                'initialPreviewAsData' => true,
                'showUpload'=>false,
                'showRemove'=>false,
                'autoReplace'=>true,
                'maxFileCount'=>1,
            ]
        ])?>
        <?php } ?>
        <p style="margin: 0 auto;text-align: center;margin-bottom: 2px;">
            <?=Html::submitButton($model->isNewRecord ? Yii::t('app', '创建') : Yii::t('app', '更新'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);?>
            <?=Html::a('返回', 'javascript:history.go(-1);location.reload()', ['class' => 'btn btn-primary','style'=>'margin-left:10px']);?>
        </p>
        <?php
            ActiveForm::end();
        ?>

    </div>
