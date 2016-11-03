<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use kartik\select2\Select2;
use admin\models\GoodVip;
use kartik\widgets\DatePicker;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodRush $model
 * @var yii\widgets\ActiveForm $form
 */
$payArr = [
    1=>'余额',
    2=>'支付宝',
    3=>'微信',
]
?>
<style>
    .radio-inline input[type="radio"] {
        margin-top: 1px;
    }
</style>
<div class="good-rush-form" style="width: 80%">
    <?php $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_VERTICAL,
        'formConfig' => [
            'deviceSize' => ActiveForm::SIZE_LARGE,
        ],
        'enableAjaxValidation'=>true, //开启ajax验证
        'validationUrl'=>Url::toRoute(['valid-form','id'=>empty($model['id'])?0:$model['id']]), //验证url
    ]);
    ?>

                <?php

                echo Form::widget([

                    'model' => $model,
                    'form' => $form,
                    'columns' => 1,
                    'attributes' => [

                        'id'=>['type'=>Form::INPUT_HIDDEN,'label'=>false],

                        'gid'=>['label'=>'商品','type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                            'options'=>[
                                'data'=>GoodVip::GetGoods(),
                                'options'=>['placeholder'=>'请选择商品'],
                                'pluginOptions' => ['allowClear' => true],
                            ],
                        ],

                        'limit'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'请填写每单最大可购数量','onkeyup'=>'this.value=this.value.replace(/\D/gi,"")']],

                        'amount'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'请填写用于抢购库存','onkeyup'=>'this.value=this.value.replace(/\D/gi,"")']],

//            'is_active'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 是否上架...']],

                        'price'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'请填写抢购价格', 'maxlength'=>10,'onkeyup'=>'clearNoNum(this)']],

                        'start_at'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>DatePicker::className(),'options'=>[
                            'language'=>'zh-CN',
                            'readonly'=>true,
                            'options' => [
                                'placeholder' => '请选择抢购开始时间',
                                'value' => empty($model->id) ? '':date('Y-m-d',$model->end_at),
                            ],
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'todayBtn' => true,
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                            ]
                        ]],

                        'end_at'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>DatePicker::className(),'options'=>[
                            'language'=>'zh-CN',
                            'readonly'=>true,
                            'options' => [
                                'placeholder' => '请选择抢购结束时间',
                                'value' => empty($model->id) ? '':date('Y-m-d',$model->end_at),
                            ],
                            'pluginOptions' => [
                                'value' => empty($model->id) ? '':date('Y-m-d',$model->end_at),
                                'todayHighlight' => true,
                                'todayBtn' => true,
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                            ]
                        ]
                        ],
                        'rush_pay'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                            'options'=>[
                                'value' => $model->rush_pay, // initial value
                                'data'=>$payArr,
                                'options'=>['placeholder'=>'抢购商品支持支付方式','multiple' => true],
                                'pluginOptions' => [
                                    'disabled'=>true,
                                    'allowClear' => true,
                                    'tags' => true,
                                    'maximumInputLength' => 3
                                ],
                            ]
                        ],

                        'point_sup'=>['type'=> Form::INPUT_RADIO_LIST,'items'=>['0'=>'不支持','1'=>'支持'],'options'=>['inline'=>true]],

                    ]

                ]);

                echo Html::submitButton(Yii::t('app', 'Save') , ['class' =>'btn btn-success']);
                ?>

    <?php
    ActiveForm::end(); ?>
</div>
<!--<script>-->
<!--    $(function () {-->
<!--        $('input[name="GoodRush[point_sup]"').attr('disabled',true);-->
<!--    })-->
<!--</script>-->
