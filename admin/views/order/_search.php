<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var admin\models\OrderInfoSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="order-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sid') ?>

    <?= $form->field($model, 'uid') ?>

    <?= $form->field($model, 'aid') ?>

    <?= $form->field($model, 'order_date') ?>

    <?php // echo $form->field($model, 'order_code') ?>

    <?php // echo $form->field($model, 'pay_id') ?>

    <?php // echo $form->field($model, 'pay_date') ?>

    <?php // echo $form->field($model, 'total') ?>

    <?php // echo $form->field($model, 'discount') ?>

    <?php // echo $form->field($model, 'ticket_id') ?>

    <?php // echo $form->field($model, 'send_bill') ?>

    <?php // echo $form->field($model, 'send_id') ?>

    <?php // echo $form->field($model, 'send_code') ?>

    <?php // echo $form->field($model, 'pay_bill') ?>

    <?php // echo $form->field($model, 'state') ?>

    <?php // echo $form->field($model, 'send_date') ?>

    <?php // echo $form->field($model, 'is_del') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
