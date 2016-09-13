<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var admin\models\PromotionInfoSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="promotion-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'pt_id') ?>

    <?= $form->field($model, 'limit') ?>

    <?= $form->field($model, 'target_id') ?>

    <?= $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'condition') ?>

    <?php // echo $form->field($model, 'discount') ?>

    <?php // echo $form->field($model, 'valid_circle') ?>

    <?php // echo $form->field($model, 'start_at') ?>

    <?php // echo $form->field($model, 'end_at') ?>

    <?php // echo $form->field($model, 'time') ?>

    <?php // echo $form->field($model, 'regist_at') ?>

    <?php // echo $form->field($model, 'is_active') ?>

    <?php // echo $form->field($model, 'active_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
