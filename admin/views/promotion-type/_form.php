<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\PromotionType $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="promotion-type-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]);
        echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'class'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>kartik\select2\Select2::className(),
                'options'=>[
                    'data'=>['1'=>'有券','2'=>'无券'],
                    'options'=>['placeholder'=>'请选择类别'],
                    'pluginOptions'=>['allowClear'=>true]
                ]
            ],

            'group'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>kartik\select2\Select2::className(),
                'options'=>[
                    'data'=>['1'=>'优惠','2'=>'特权','3'=>'赠送'],
                    'options'=>['placeholder'=>'请选择组别'],
                    'pluginOptions'=>['allowClear'=>true]
                ]
            ],

            'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'填写优惠券名称', 'maxlength'=>128]],

            'is_active'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>kartik\select2\Select2::className(),
                'options'=>[
                    'data'=>['0'=>'下架','1'=>'上架'],
                    'options'=>['placeholder'=>'请选择是否上架'],
                    'pluginOptions'=>['allowClear'=>true]
                ]
            ],

        ]

    ]);
    ?>

    <p style="margin: 0 auto;text-align: center;margin-bottom: 2px;">
        <?=Html::submitButton($model->isNewRecord ? Yii::t('app', '创建') : Yii::t('app', '更新'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);?>
        <?=Html::a('返回', 'javascript:history.go(-1);location.reload()', ['class' => 'btn btn-primary','style'=>'margin-left:10px']);?>
    </p>

    <?php ActiveForm::end(); ?>

</div>
