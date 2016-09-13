<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\PromotionInfo $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="promotion-info-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'pt_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Pt ID...']],

            'limit'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Limit...']],

            'target_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Target ID...']],

            'valid_circle'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Valid Circle...']],

            'start_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Start At...']],

            'end_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter End At...']],

            'time'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Time...']],

            'regist_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Regist At...']],

            'is_active'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Is Active...']],

            'active_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Active At...']],

            'condition'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Condition...', 'maxlength'=>11]],

            'discount'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Discount...', 'maxlength'=>11]],

            'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Name...', 'maxlength'=>128]],

        ]

    ]);

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
