<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\select2\Select2;
use admin\models\Dics;
use yii\helpers\Url;

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

            'env'=>['type'=> Form::INPUT_WIDGET,'label'=>'促销环境','widgetClass'=>Select2::className(),
                'options'=>[
                    'data'=>Dics::getPromotionEnv(),
                    'options'=>['placeholder'=>'请选择促销环境'],
                    'pluginOptions'=>['allowClear'=>true]
                ]
            ],

            'group'=>['type'=> Form::INPUT_WIDGET,'label'=>'促销形式','widgetClass'=>Select2::className(),
                'options'=>[
                    'data'=>Dics::getPromotionGroup(),
                    'options'=>['placeholder'=>'请选择促销形式'],
                    'pluginOptions'=>['allowClear'=>true]
                ]
            ],

            'limit'=>['type'=> Form::INPUT_WIDGET,'label'=>'促销限制','widgetClass'=>Select2::className(),
                'options'=>[
                    'data'=>Dics::getPromotionLimit(),
                    'options'=>['placeholder'=>'请选择促销限制'],
                    'pluginOptions'=>['allowClear'=>true]
                ]
            ],


        ]

    ]);


    echo Html::submitButton(Yii::t('app', 'Save') , ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
