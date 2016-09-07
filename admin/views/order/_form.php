<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\OrderInfo $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="order-info-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'sid'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Sid...']],

            'uid'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Uid...']],

            'aid'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Aid...']],

            'order_date'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Order Date...']],

            'pay_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Pay ID...']],

            'pay_date'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Pay Date...']],

            'ticket_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Ticket ID...']],

            'send_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Send ID...']],

            'state'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter State...']],

            'send_date'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Send Date...']],

            'is_del'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Is Del...']],

            'status'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Status...']],

            'total'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Total...', 'maxlength'=>10]],

            'discount'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Discount...', 'maxlength'=>10]],

            'send_bill'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Send Bill...', 'maxlength'=>10]],

            'pay_bill'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Pay Bill...', 'maxlength'=>10]],

            'order_code'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Order Code...', 'maxlength'=>16]],

            'send_code'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Send Code...', 'maxlength'=>12]],

        ]

    ]);

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
