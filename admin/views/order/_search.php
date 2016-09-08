<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
/**
 * @var yii\web\View $this
 * @var admin\models\OrderInfoSearch $model
 * @var yii\widgets\ActiveForm $form
 */
$pay_types=\yii\helpers\ArrayHelper::map(\admin\models\OrderInfoSearch::getPaytype(),'id','name');
?>

<div class="order-info-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options'=>[
            'class'=>'form-inline',
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['style'=>['width'=>'100px']])?>

    <?= $form->field($model, 'nickname')->textInput(['style'=>['width'=>'100px']]) ?>

    <?= $form->field($model, 'order_date')->widget(DatePicker::classname(),[
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ]) ?>

    <?= $form->field($model, 'order_code') ?>
    <?= $form->field($model, 'send_code') ?>
    <?= $form->field($model, 'pay_id')->dropDownList($pay_types,['prompt'=>'全部']) ?>

    <?= $form->field($model, 'total')->textInput(['style'=>['width'=>'50px']]) ?>

    <?= $form->field($model, 'is_ticket')->dropDownList(['0'=>'无','1'=>'有']) ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary','style'=>'margin-top:-8px']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default','style'=>'margin-top:-8px']) ?>
    </div>

    <?php ActiveForm::end();
   
    ?>

</div>

<?//=Html::a('<i class="glyphicon glyphicon-plus"></i>新增订单', ['create'], ['class' => 'btn btn-success'])?>