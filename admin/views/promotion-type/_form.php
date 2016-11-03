<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\select2\Select2;
use admin\models\Dics;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;

/**
 * @var yii\web\View $this
 * @var admin\models\PromotionType $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="promotion-type-form">

    <?php $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_VERTICAL,
        'fullSpan'=>12,
        'formConfig' => [
            'deviceSize' => ActiveForm::SIZE_LARGE,
        ],
        'enableAjaxValidation'=>true, //开启ajax验证
        'validationUrl'=>Url::toRoute(['valid-form','id'=>empty($model->id)?0:$model->id]), //验证url
    ]);
    ?>
    <div class="panel panel-info" style="width: 80%">
        <div class="panel-heading">
            <?= $model->isNewRecord ? '发布促销类型' : '编辑促销类型'.$model->name ?>
        </div>
        <div class="panel-body">
    <?php
        echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'name'=>['type'=> Form::INPUT_TEXT,'label'=>'类型名', 'options'=>['placeholder'=>'填写类型名', 'maxlength'=>128]],

            'class'=>['type'=> Form::INPUT_WIDGET,'label'=>'促销组别','widgetClass'=>Select2::className(),
                'options'=>[
                    'data'=>Dics::getPromotionClass(),
                    'options'=>['placeholder'=>'请选择组别'],
                    'pluginOptions'=>['allowClear'=>true]
                ]
            ],

            'env'=>['type'=> Form::INPUT_WIDGET,'label'=>'促销环境','widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=> empty($model->id) ? []:Dics::getPromotionEnv(),
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'options'=>['placeholder'=>'请选择促销环境',empty($model->id) ? '':'disabled'=>true],
                    'pluginOptions'=>[
                        'placeholder'=>'请选择促销环境',
                        'depends'=>['promotiontype-class'],
                        'url' => Url::toRoute(['promotion-type/env']),
                        'loadingText' => '',
                    ],
                ],
//                'options'=>[
//                    'data'=>Dics::getPromotionEnv(),
//                    'options'=>['placeholder'=>'请选择促销环境'],
//                    'pluginOptions'=>['allowClear'=>true]
//                ]
            ],

            'group'=>['type'=> Form::INPUT_WIDGET,'label'=>'促销形式','widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=> empty($model->id) ? []:Dics::getPromotionGroup(),
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'options'=>['placeholder'=>'请选择促销形式',empty($model->id) ? '':'disabled'=>true],
                    'pluginOptions'=>[
                        'placeholder'=>'请选择促销形式',
                        'depends'=>['promotiontype-class','promotiontype-env'],
                        'url' => Url::toRoute(['promotion-type/group']),
                        'loadingText' => '',
                    ],
                ],
            ],

            'limit'=>['type'=> Form::INPUT_WIDGET,'label'=>'促销限制','widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=> empty($model->id) ? []:Dics::getPromotionGroup(),
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'options'=>['placeholder'=>'请选择促销形式',empty($model->id) ? '':'disabled'=>true],
                    'pluginOptions'=>[
                        'placeholder'=>'请选择促销形式',
                        'depends'=>['promotiontype-class','promotiontype-env','promotiontype-group'],
                        'url' => Url::toRoute(['promotion-type/limit']),
                        'loadingText' => '',
                    ],
                ],
            ],


        ]

    ]);


    echo Html::submitButton(Yii::t('app', 'Save') , ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
