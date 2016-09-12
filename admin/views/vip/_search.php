<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodVipSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="good-vip-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'options'=>[
            'class'=>'form-inline',
        ],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'gid') ?>

    <?= $form->field($model, 'price') ?>

    <?= $form->field($model, 'is_active') ?>

    <div class="form-group" style="height: 44px">
        <?= Html::submitButton('搜 索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重 置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
echo Html::a('<i class="glyphicon glyphicon-plus"></i> 添加会员商品', ['create'], ['class' => 'btn btn-success']);
?>
