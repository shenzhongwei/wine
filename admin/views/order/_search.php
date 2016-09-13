<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use admin\models\OrderInfoSearch;

$pay_types=ArrayHelper::map(OrderInfoSearch::getPaytype(),'id','name');
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

    <?= $form->field($model, 'nickname')->textInput(['style'=>['width'=>'100px']])->label('下单用户') ?>

    <?= $form->field($model, 'order_date_from')->widget(DatePicker::classname(),[
        'options' => ['placeholder' => '','style'=>['width'=>'120px']],
        'pluginOptions' => [
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ])->label('下单时间：从') ?>

    <?= $form->field($model, 'order_date_to')->widget(DatePicker::classname(),[
        'options' => ['placeholder' => '','style'=>['width'=>'120px']],
        'pluginOptions' => [
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ])->label('至') ?>

    <?= $form->field($model, 'order_code')->textInput(['style'=>['width'=>'100px']]) ?>

    <?= $form->field($model, 'send_code')->textInput(['style'=>['width'=>'100px']]) ?>

    <?= $form->field($model, 'pay_id')->dropDownList($pay_types,['prompt'=>'全部']) ?>

    <?= $form->field($model, 'is_ticket')->dropDownList(['0'=>'无','1'=>'有']) ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary','style'=>'margin-top:-8px']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-warning','style'=>'margin-top:-8px']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>

<?//=Html::a('<i class="glyphicon glyphicon-plus"></i>新增订单', ['create'], ['class' => 'btn btn-success'])?>