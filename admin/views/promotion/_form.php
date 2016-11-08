<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\select2\Select2;
use admin\models\PromotionInfo;
use admin\models\Dics;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use kartik\widgets\DatePicker;
/**
 * @var yii\web\View $this
 * @var admin\models\PromotionInfo $model
 * @var yii\widgets\ActiveForm $form
 */
//优惠分类
$this->registerJs($this->render('_script.js'));
?>
<style>
    .radio-inline input[type="radio"] {
        margin-top: 1px;
    }
</style>
<div class="promotion-info-form" style="width: 80%">
    <?php
    $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_VERTICAL,
        'formConfig' => [
            'deviceSize' => ActiveForm::SIZE_LARGE,
        ],

        'enableAjaxValidation'=>true, //开启ajax验证
        'validationUrl'=>Url::toRoute(['valid-form','id'=>empty($model->id)?0:$model->id]), //验证url
    ]);
    echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 2,
        'attributes' => [

//            'id'=>['type'=>Form::INPUT_HIDDEN,'label'=>false],

            'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'输入活动名称', 'maxlength'=>30]],


            'date_valid'=>['type'=> Form::INPUT_RADIO_LIST,'items'=>['0'=>'无期限','1'=>'有期限'],
                'options'=>[
                    'inline'=>true,
                ],
            ],



            'pt_id'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                'options'=>[
                    'data'=>PromotionInfo::getAllTypes($model->isNewRecord ? 'create':'update'),
                    'options'=>['placeholder'=>'请选择促销种类'],
                    'pluginOptions'=>['allowClear'=>true],
                    'pluginEvents'=>[
                        'change'=>"function() { TypeChange(this); }",
                    ]
                ]
            ],

            'start_at'=>['type'=>Form::INPUT_WIDGET,'widgetClass'=>DatePicker::className(),'options'=>[
                'language'=>'zh-CN',
                'readonly'=>true,
                'options' => [
                    'placeholder' => $model->isNewRecord ?'请先选择活动期限形式':($model->date_valid==1 ? '该形式无需选择开始日期':'请选择活动开始日期'),
                    'value' => empty($model->id) ? '':date('Y-m-d',$model->start_at),
                    'disabled'=>$model->isNewRecord ? true:($model->date_valid==1 ? false:true),
                ],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]],

            'limit'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type'=>DepDrop::TYPE_SELECT2,
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true],'hideSearch'=>true],
                    'data'=>$model->isNewRecord ? []:Dics::getPromotionRange($model->pt_id),
                    'pluginOptions'=>[
                        'placeholder'=>'请选择适用范围',
                        'disabled'=>true,
                        'depends'=>['promotioninfo-pt_id'],
                        'url' => Url::toRoute(['promotion/limit']),
                        'loadingText' => '',
                    ],
                ]
            ],

            'end_at'=>['type'=>Form::INPUT_WIDGET,'widgetClass'=>DatePicker::className(),'options'=>[
                'language'=>'zh-CN',
                'readonly'=>true,
                'options' => [
                    'placeholder' => $model->isNewRecord ?'请先选择活动期限形式':($model->date_valid==1 ? '该形式无需选择结束日期':'请选择活动结束日期'),
                    'value' => empty($model->id) ? '':date('Y-m-d',$model->end_at),
                    'disabled'=>$model->isNewRecord ? true:($model->date_valid==1 ? false:true),
                ],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]],

            'target_id'=>['type'=>Form::INPUT_WIDGET,'label'=>'适用对象','widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=>empty($model->limit)? [] : PromotionInfo::getTargetsRange($model->limit),
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'placeholder'=>'选择适用对象',
                        'depends'=>['promotioninfo-limit'],
                        'url' =>Url::toRoute(['promotion/targets']),
                        'loadingText' => '',
                    ]
                ]
            ],

            'time_valid'=>['type'=> Form::INPUT_RADIO_LIST,'items'=>['0'=>'不限制次数','1'=>'限制次数'],'options'=>['inline'=>true,]
            ],

            'style'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=>empty($model->pt_id)? [] : PromotionInfo::GetStyles($model->pt_id),
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true],'hideSearch'=>true],
                    'pluginOptions'=>[
                        'placeholder'=>'请选择优惠形式',
                        'depends'=>['promotioninfo-pt_id'],
                        'url' =>Url::toRoute(['promotion/styles']),
                        'loadingText' => '',
                    ],
                ]
            ],

            'time'=>['type'=> Form::INPUT_TEXT ,'label'=>'可参与次数', 'options'=>[
                'placeholder'=>$model->isNewRecord ? '请先选择参与次数形式':($model->time_valid==1 ? '输入可参与次数':'该形式无需输入参与次数'),
                'disabled'=>$model->isNewRecord ? true:($model->time_valid==1 ? false:true),
                'onkeyup'=>'this.value=this.value.replace(/\D/gi,"")'
            ]],

            'condition'=>['type'=> Form::INPUT_TEXT, 'label'=>'活动条件','options'=>[
                'placeholder'=>$model->isNewRecord ? '请先选择优惠形式':($model->style==2 ? '该类型无需输入优惠条件':'输入优惠条件'),
                'maxlength'=>10,'onkeyup'=>'clearNoNum(this)',
                'disabled'=>$model->isNewRecord ? true:($model->style==2 ? true:false),
            ]],

            'circle_valid'=>['type'=> Form::INPUT_RADIO_LIST,'items'=>['0'=>'永久有效','1'=>'非永久有效'],'options'=>['inline'=>true]],

            'discount'=>['type'=> Form::INPUT_TEXT, 'label'=>'活动优惠', 'options'=>[
                'placeholder'=>$model->isNewRecord ? '请先选择优惠形式':($model->style==2 ? '输入所占百分比':'输入优惠额度'),
                'maxlength'=>10,'onkeyup'=>'clearNoNum(this)',
                'disabled'=>$model->isNewRecord ? true:false,
            ]],

            'valid_circle'=>['type'=> Form::INPUT_TEXT, 'label'=>'优惠券有效期限（单位：天）','options'=>[
                'placeholder'=>$model->isNewRecord ? '请先选择参与优惠券期限形式':($model->circle_valid==1 ? '输入优惠券有效期(单位：天)':'该形式无需输入优惠券的有效期'),
                'disabled'=>$model->isNewRecord ? true:($model->circle_valid==1 ? false:true),
                'maxlength'=>10,
                'onkeyup'=>'this.value=this.value.replace(/\D/gi,"")'
            ]],

        ]
    ]);

    ?>

    <?=Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']);?>
    <?php  ActiveForm::end(); ?>

</div>