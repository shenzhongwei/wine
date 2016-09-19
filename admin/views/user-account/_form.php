<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\UserAccount $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="user-account-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'target'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Target...']],

            'level'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Level...']],

            'type'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Type...']],

            'create_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Create At...']],

            'is_active'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Is Active...']],

            'update_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Update At...']],

            'start'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Start...', 'maxlength'=>11]],

            'end'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter End...', 'maxlength'=>11]],

            'pay_password'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Pay Password...', 'maxlength'=>100]],

        ]

    ]);

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
