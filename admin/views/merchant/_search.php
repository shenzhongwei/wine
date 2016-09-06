<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;

/**
 * @var yii\web\View $this
 * @var admin\models\MerchantInfoQuery $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="merchant-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options'=>[
            'class'=>'form-inline',
        ],
    ]); ?>

    <?= $form->field($model, 'name')->widget(AutoComplete::className(),[
        'clientOptions' => [
            'source' =>$mername,
        ],
    ])->textInput()  ?>

    <?= $form->field($model, 'region') ?>

    <?= $form->field($model, 'phone') ?>
    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary','style'=>'margin-top:-8px']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default','style'=>'margin-top:-8px']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?=Html::a('<i class="glyphicon glyphicon-plus"></i>新增商户', ['create'], ['class' => 'btn btn-success'])?>
