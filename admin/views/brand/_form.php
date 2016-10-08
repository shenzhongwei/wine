<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodBrand $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="good-brand-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'type'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 类型id...']],

            'regist_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 添加时间...']],

            'is_active'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 是否上架...']],

            'active_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 上架状态更改时间...']],

            'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 品牌名...', 'maxlength'=>50]],

            'logo'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 品牌log...', 'maxlength'=>128]],

        ]

    ]);

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
