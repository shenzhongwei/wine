<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\UserInfo $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="user-info-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'sex'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Sex...']],

            'invite_user_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Invite User ID...']],

            'is_vip'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Is Vip...']],

            'status'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Status...']],

            'created_time'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>DateControl::classname(),'options'=>['type'=>DateControl::FORMAT_DATETIME]],

            'updated_time'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>DateControl::classname(),'options'=>['type'=>DateControl::FORMAT_DATETIME]],

            'phone'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Phone...', 'maxlength'=>13]],

            'head_url'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Head Url...', 'maxlength'=>128]],

            'birth'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Birth...', 'maxlength'=>255]],

            'nickname'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Nickname...', 'maxlength'=>32]],

            'realname'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Realname...', 'maxlength'=>32]],

            'invite_code'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Invite Code...', 'maxlength'=>32]],

        ]

    ]);

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
