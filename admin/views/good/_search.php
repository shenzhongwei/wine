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
        'options'=>[
            'class'=>'form-inline',
        ],
    ]); ?>

    <?php  echo $form->field($model, 'type') ?>

    <?php  echo $form->field($model, 'name') ?>

    <?php  echo $form->field($model, 'volum') ?>

    <?php  echo $form->field($model, 'price') ?>

    <?php  echo $form->field($model, 'number') ?>

    <?php  echo $form->field($model, 'is_active') ?>

    <div class="form-group" style="height: 44px">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
<?php
echo Html::a('<i class="glyphicon glyphicon-plus"></i> 发布商品', ['create'], ['class' => 'btn btn-success']);
?>
