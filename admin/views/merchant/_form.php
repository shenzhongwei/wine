<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use yii\helpers\ArrayHelper;
use admin\models\Zone;
/**
 * @var yii\web\View $this
 * @var admin\models\MerchantInfo $model
 * @var yii\widgets\ActiveForm $form
 */

//省
$province=ArrayHelper::map(Zone::getProvince(),'id','name');
//var_dump($province);exit;
$city=[];
$district=[];

?>

<script>
    //选中省份后，修改城市
    function changeProvince(){
        //获取选中的省份
        var p=$('#merchantinfo-province').val();
        var html='<option value="0">--市--</option>';
        $('#merchantinfo-city').attr('disabled',false);
        //修改市
        $.ajax({
            url:'selectcity',
            data:{'p_id':p},
            type:'post',
            dataType:'json',
            success:function(msg){
                for(var i=0;i<msg.length;i++){
                    html+='<option value="'+msg[i].id+'">'+msg[i].name+'</option>';
                }
                $('#merchantinfo-city').empty().append(html);
                changeCity();
            }
        }
        );
    }
    //选中城市后，修改区域
    function changeCity(){
        //获取选中的省份
        var c=$('#merchantinfo-city').val();
        var html='<option value="0">--区--</option>';
        $('#merchantinfo-district').attr('disabled',false);
        //修改市
        $.ajax({
                url:'selectdistrict',
                data:{'c_id':c},
                type:'post',
                dataType:'json',
                success:function(msg){
                    for(var i=0;i<msg.length;i++){
                        html+='<option value="'+msg[i].id+'">'+msg[i].name+'</option>';
                    }
                    $('#merchantinfo-district').empty().append(html);
                }
            }
        );

    }
</script>
<div class="merchant-info-form">

    <?php
        $form = ActiveForm::begin(
            [
                'type'=>ActiveForm::TYPE_HORIZONTAL
            ]
        );
    ?>
    <div class="merchant-form" style="margin-top: 10px;width: 90%;">

        <?= $form->field($model, 'name')->textInput(['maxlength' =>128])?>
        <?= $form->field($model, 'phone')->textInput(['maxlength' =>11])?>
        <?= $form->field($model, 'province')->dropDownList($province,['prompt'=>'--省--','onchange'=>'javascript:changeProvince();'])?>
        <?= $form->field($model, 'city')->dropDownList($city,['prompt'=>'--市--','disabled'=>true,'onchange'=>'javascript:changeCity();'])?>
        <?= $form->field($model, 'district')->dropDownList($district,['prompt'=>'--区--','disabled'=>true])?>
        <?= $form->field($model, 'region')->textInput(['maxlength' =>50])->label('填写门牌号') ?>
        <hr>
        <?= $form->field($model, 'wa_username')->textInput(['maxlength' =>50])?>
        <?= $form->field($model, 'wa_password')->textInput(['maxlength' =>50])?>
        <?= $form->field($model, 'wa_type')->dropDownList($item_arr,['prompt'=>'商家管理员','disabled'=>true]) ?>
<!--        <input type="hidden" id="appliance-logoUrl" class="form-control"  name="Appliance[logoUrl]" value="--><?//=$model->logo?><!--">-->
        <?= $form->field($model, 'wa_logo')->widget(\kartik\file\FileInput::className(),[
            'options'=>[
                'accept'=>'image/*',
            ],

            'pluginOptions'=>[

//                // 需要预览的文件格式
//                'previewFileType' => 'image',
//                // 预览的文件
//                'initialPreview' =>$p1,
//                // 需要展示的图片设置，比如图片的宽度等s
//                'initialPreviewConfig' =>$PreviewConfig,
//                // 是否展示预览图
//                'initialPreviewAsData' => true,
//                //异步上传接口地址
//                'uploadUrl'=>Url::toRoute(['appliance/upload?type=logo']),

                'showUpload'=>false,
                'showRemove'=>false,
                'autoReplace'=>true,

                'maxFileCount'=>1,
                // 如果要设置具体图片上的移除、上传和展示按钮，需要设置该选项
//                'fileActionSettings' => [
//                    // 设置具体图片的上传属性为true,默认为true
//                    'showUpload' =>true,
//                    // 设置具体图片的移除属性为true,默认为true
//                    'showRemove' =>true,
//                ],
            ],
//            'pluginEvents' => [
//                // 上传成功后的回调方法，
//                "fileuploaded" => "function(event, data, previewId, index){
//                       var response = data.response;
//                       $('#appliance-logoUrl').val(response.res_url);
//                   }",
//                'change'=>'function(){$(this).fileinput("upload").fileinput("disable");}'
//            ]

        ])?>

    </div>

    <?php
    ActiveForm::end();
    ?>
    <p style="margin: 0 auto;text-align: center;margin-bottom: 2px;">
        <?=Html::submitButton($model->isNewRecord ? Yii::t('app', '创建') : Yii::t('app', '更新'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);?>
        <?=Html::a('返回', 'javascript:history.go(-1);location.reload()', ['class' => 'btn btn-primary','style'=>'margin-left:10px']);?>
    </p>
</div>
