<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="good-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'merchant') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'brand') ?>

    <?= $form->field($model, 'smell') ?>

    <?php // echo $form->field($model, 'color') ?>

    <?php // echo $form->field($model, 'dry') ?>

    <?php // echo $form->field($model, 'boot') ?>

    <?php // echo $form->field($model, 'breed') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'style') ?>

    <?php // echo $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'volum') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'unit') ?>

    <?php // echo $form->field($model, 'pic') ?>

    <?php // echo $form->field($model, 'number') ?>

    <?php // echo $form->field($model, 'detail') ?>

    <?php // echo $form->field($model, 'order') ?>

    <?php // echo $form->field($model, 'regist_at') ?>

    <?php // echo $form->field($model, 'is_active') ?>

    <?php // echo $form->field($model, 'active_at') ?>

    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
