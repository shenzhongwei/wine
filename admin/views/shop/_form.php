<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use kartik\select2\Select2;
use admin\models\ShopInfo;
use kartik\file\FileInput;

/**
 * @var yii\web\View $this
 * @var admin\models\ShopInfo $model
 * @var kartik\widgets\ActiveForm $form
 */
$model->wa_password = $model->isNewRecord ? '':'******';
$model->wa_username = $model->isNewRecord ? '':$model->wa_username;
?>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=<?=Yii::$app->params['key'] ?>&plugin=AMap.Autocomplete"></script>
<div class="good-rush-form">
    <?php $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_VERTICAL,
        'formConfig' => [
            'deviceSize' => ActiveForm::SIZE_LARGE,
        ],
        'enableAjaxValidation'=>true, //开启ajax验证
        'validationUrl'=>Url::toRoute(['valid-form','id'=>empty($model['id'])?0:$model['id']]), //验证url
    ]);
    ?>
    <div class="row">
        <div class="col-sm-6">
            <?php
            echo Form::widget([

                'model' => $model,
                'form' => $form,
                'columns' => 1,
                'attributes' => [
                    'name'=>['type'=>Form::INPUT_TEXT,'label'=>'门店名称','options'=>['placeholder'=>'请填写门店名','maxlength'=>20]],

                    'merchant'=>['type'=>Form::INPUT_WIDGET,'label'=>'所属商户','widgetClass'=>Select2::className(),
                        'options'=>[
                            'data'=>ShopInfo::GetMerchants($model->isNewRecord ? 'create':'update'),
                            'options'=>['placeholder'=>'请选择商品'],
                            'pluginOptions' => ['allowClear' => true],
                        ],],

                    'contacter'=>['type'=>Form::INPUT_TEXT,'label'=>'联系人','options'=>['placeholder'=>'请填写联系人','maxlength'=>20]],

                    'phone'=>['type'=>Form::INPUT_TEXT,'label'=>'联系电话','options'=>[
                        'placeholder'=>'请填写门店电话','maxlength'=>16,'onkeyup'=>'this.value=this.value.replace(/\D/gi,"")'
                    ]],

                    'region'=>['type'=>Form::INPUT_TEXT,'label'=>'门店地区','options'=>[
                        'placeholder'=>'请填写门店地区','readOnly'=>true,
                        'data-toggle' => 'modal',    //弹框
                        'data-target' => '#shop-modal',    //指定弹框的id
                    ]],

                    'address'=>['type'=>Form::INPUT_TEXT,'label'=>'详细地址','options'=>['placeholder'=>'请填写详细地址','maxlength'=>36]],
                ]

            ]);


            echo $form->field($model,'province')->hiddenInput()->label(false);
            echo $form->field($model,'city')->hiddenInput()->label(false);
            echo $form->field($model,'district')->hiddenInput()->label(false);
            echo $form->field($model,'lat')->hiddenInput()->label(false);
            echo $form->field($model,'lng')->hiddenInput()->label(false);
            echo Form::widget([

                'model' => $model,
                'form' => $form,
                'columns' => 1,
                'attributes' => [
                    'limit'=>['type'=>Form::INPUT_TEXT,'label'=>'配送范围','options'=>[
                        'placeholder'=>'请填写配送范围(单位：米；整数)','maxlength'=>10,'onkeyup'=>'clearNoNum(this)'
                    ]],

                    'least_money'=>['type'=>Form::INPUT_TEXT,'label'=>'下单最低金额','options'=>[
                        'placeholder'=>'请填写最低金额','maxlength'=>10,'onkeyup'=>'clearNoNum(this)'
                    ]],

                    'send_bill'=>['type'=>Form::INPUT_TEXT,'label'=>'运费','options'=>[
                        'placeholder'=>'请填写运费','maxlength'=>10,'onkeyup'=>'clearNoNum(this)'
                    ]],

                    'no_send_need'=>['type'=>Form::INPUT_TEXT,'label'=>'免配送订单金额','options'=>[
                        'placeholder'=>'请填写免配送订单金额','maxlength'=>10,'onkeyup'=>'clearNoNum(this)'
                    ]],
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
                'attributes' => [
                    'img'=>[
                        'label'=>'营业执照',
                        'type'=> Form::INPUT_WIDGET, 'widgetClass'=>FileInput::className(),
                        'options'=>[
                            'options'=>[
                                'accept'=>'image/*',
                                'showUpload'=>false,
                                'showRemove'=>false,
                            ],
                            'pluginOptions'=>[
                                'initialPreview'=>empty($model->bus_pic) ? false:[
                                    "../../../photo".$model->bus_pic,
                                ],
                                'uploadUrl' => Url::to(['/shop/upload']),
                                'uploadExtraData' => [
                                    'key'=>'bus',
                                    'attr'=>'img',
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
                                    $('#shopinfo-bus_pic').val('');
                                    }",
                                'fileuploaded'  => "function (object,data){
			                    $('#shopinfo-bus_pic').val(data.response.imageUrl);
		                    }",
                                //错误的冗余机制
                                'error' => "function (){
			                    alert('data.error');
		                    }"
                            ]
                        ],
                    ],
                ]

            ]);

            echo $form->field($model,'bus_pic')->hiddenInput()->label(false);
            echo Form::widget([

                'model' => $model,
                'form' => $form,
                'columns' => 1,
                'attributes' => [

                    'url'=>[
                        'label'=>'门店logo',
                        'type'=> Form::INPUT_WIDGET, 'widgetClass'=>FileInput::className(),
                        'options'=>[
                            'options'=>[
                                'accept'=>'image/*',
                                'showUpload'=>false,
                                'showRemove'=>false,
                            ],
                            'pluginOptions'=>[
                                'initialPreview'=>empty($model->logo) ? false:[
                                    "../../../photo".$model->logo,
                                ],
                                'uploadUrl' => Url::to(['/shop/upload']),
                                'uploadExtraData' => [
                                    'key'=>'logo',
                                    'attr'=>'url',
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
                                    $('#shopinfo-logo').val('');
                                    }",
                                'fileuploaded'  => "function (object,data){
			                    $('#shopinfo-logo').val(data.response.imageUrl);
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
            echo $form->field($model,'logo')->hiddenInput()->label(false);
            echo Form::widget([

                'model' => $model,
                'form' => $form,
                'columns' => 1,
                'attributes' => [

                    'wa_username'=>['type'=>Form::INPUT_TEXT,'label'=>'后台登录名','options'=>[
                        'placeholder'=>'请填写后台登录名','maxlength'=>10,$model->isNewRecord ? '':'readOnly'=>true,
                    ]],

                    'wa_password'=>['type'=>Form::INPUT_PASSWORD,'label'=>'后台登录密码','options'=>[
                        'placeholder'=>'请填写后台登录密码','maxlength'=>16,$model->isNewRecord ? '':'readOnly'=>true,
                    ]],
                ]

            ]);
            ?>

        </div>
    </div>
    <?php
    echo '<p style="text-align: center">'.
     Html::submitButton(Yii::t('app', 'Save') , ['class' =>'btn btn-success',]).'</p>';
    ?>

    <?php
    ActiveForm::end(); ?>
</div>
<style>
    .modal-body{
        height: 600px;
        padding: 0px;
    }
    .modal-footer{
        text-align: center;
    }
    #container{
        height: 100%;
    }
    #search {
        position: absolute;
        top: 5px;
        left: 0.5%;
        background: #fff none repeat scroll 0 0;
        margin: 0.5% auto;
        /*padding: 6px;*/
        z-index: 999;
        width: 260px;
    }
    .amap-sug-result{
        z-index: 9999;
    }
</style>
<!--查看看详情弹出框  start-->
<?php

\yii\bootstrap\Modal::begin([
    'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    'id' => 'shop-modal',
    'header' => '<h4 class="modal-title">高德地图</h4>',
    'footer' => '<button class="btn btn-success" data-dismiss="modal" id="confirm">确 定</button>'.
        '<button class="btn btn-primary" style="margin-left: 10%;" data-dismiss="modal">关 闭</button>',
    'options' => [
        'tabindex' => false,
    ],
]);
\yii\bootstrap\Modal::end();
?>
<!--查看看详情弹出框  end-->
<script>
    $('#shopinfo-region').on('click', function () {  //查看详情的触发事件
        $.get(toRoute('shop/map'), {},
            function (data) {
                $('#shop-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
            }
        );
    });
</script>
