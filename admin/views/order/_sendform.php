<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\select2\Select2;
use admin\models\EmployeeInfo;
use yii\helpers\Url;
/**
 * @var yii\web\View $this
 * @var admin\models\OrderSend $model
 * @var yii\widgets\ActiveForm $form
 */
?>
<style>
    .select2-dropdown--below{
        z-index: 99999;
    }
    .radio-inline input[type="radio"] {
        margin-top: 1px;
    }
</style>
<div class="send-list-form">

    <?php $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_VERTICAL,
        'id'=>'send-form',
        'formConfig' => [
            'deviceSize' => ActiveForm::SIZE_SMALL,
        ],
        'action' => Url::toRoute(['order/send-order']),
    ]);
    ?>

    <?php

    echo Form::widget([
        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [
            'send_id'=>['type'=>Form::INPUT_WIDGET, 'label'=>'配送员','widgetClass'=>Select2::className(),'options'=>[
                'data' => EmployeeInfo::GetAllEmployee(),
                'options' => ['placeholder' => '请选择配送员'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ],],
            'send_now'=>['type'=> Form::INPUT_RADIO_LIST,'label'=>'是否马上配送','items'=>['2'=>'马上配送','1'=>'继续派单'],'options'=>['inline'=>true]],
        ]
    ]);
    echo $form->field($model,'id_str')->hiddenInput(['readonly'=>true])->label(false);
    ?>

    <?php
    echo "<div style='text-align: center'>".Html::submitButton('确定', [ 'class'=>'btn btn-success'])."</div>";
    ActiveForm::end(); ?>
</div>
