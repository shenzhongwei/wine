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

    <?= $form->field($model, 'good_name')->textInput(['style'=>'margin-right:10px'])->label('商品名') ?>

    <?= $form->field($model, 'start_price')->textInput(['style'=>'width:80px','onkeyup'=>'this.value=this.value.replace(/\D/gi,"")'])->label('价格') ?>
    至
    <?= $form->field($model, 'end_price')->textInput(['style'=>'margin-right:10px;width:80px','onkeyup'=>'this.value=this.value.replace(/\D/gi,"")'])->label(false) ?>

    <?= $form->field($model, 'is_active')->radioList([0=>'未上架',1=>'上架中'],['style'=>'margin-right:10px'])->label(false) ?>

    <div class="form-group" style="height: 44px">
        <?= Html::submitButton('搜 索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重 置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
echo Html::a('<i class="glyphicon glyphicon-plus"></i> 添加会员商品', ['create'], ['class' => 'btn btn-success']);
?>
