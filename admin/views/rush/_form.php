<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use kartik\select2\Select2;
use admin\models\GoodVip;
use dosamigos\datetimepicker\DateTimePicker;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodRush $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="good-rush-form">
    <div class="panel panel-info" style="width: 70%">
        <div class="panel-heading">
            <?= $model->isNewRecord ? '发布抢购' : '编辑抢购' ?>
        </div>
        <div class="panel-body">
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

//            'amount'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 抢购数量...']],

//            'is_active'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 是否上架...']],

                        'price'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'请填写抢购价格', 'maxlength'=>10,'onkeyup'=>'clearNoNum(this)']],

                        'start_at'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>DateTimePicker::className(),'options'=>[
                            'value' => empty($model->id) ? '':strtotime($model->start_at),
                            // inline too, not bad
                            'inline' => false,
                            'language'=>'zh-CN',
                            'options'=>[
                                'readonly'=>true,
                            ],
                            'template'=>"{button}{reset}{input}",
                            // modify template for custom rendering
                            'clientOptions' => [
                                'autoclose' => true,
                                'format'=>'hh:ii:00',
                                'startView'=>1,
                                'maxView'=>1,
                                'keyboardNavigation'=>false,
                                'showMeridian'=>true,
                                'minuteStep'=>10,
                                'forceParse'=>false,
                                'readonly'=>true,
                            ]
                        ]],

                        'end_at'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>DateTimePicker::className(),'options'=>[
                            'value' => empty($model->id) ? '':strtotime($model->end_at),
                            // inline too, not bad
                            'inline' => false,
                            'language'=>'zh-CN',
                            'options'=>[
                                'readonly'=>true,
                            ],
                            'template'=>"{button}{reset}{input}",
                            // modify template for custom rendering
                            'clientOptions' => [
                                'autoclose' => true,
                                'format'=>'hh:ii:00',
                                'startView'=>1,
                                'maxView'=>1,
                                'keyboardNavigation'=>false,
                                'showMeridian'=>true,
                                'minuteStep'=>10,
                                'forceParse'=>false,
                                'readonly'=>true,
                            ]
                        ]
                        ],

                    ]

                ]);

                echo Html::submitButton(Yii::t('app', 'Save') , ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                ?>

    <?php
    ActiveForm::end(); ?>
            </div>

        </div>
</div>
