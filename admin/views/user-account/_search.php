<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use admin\models\Dics;
/**
 * @var yii\web\View $this
 * @var admin\models\UserAccountSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="user-account-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options'=>[
            'class'=>'form-inline',
        ],
    ]); ?>

    <?= $form->field($model, 'target_name') ?>

    <?= $form->field($model, 'level')->dropDownList(['1'=>'管理员','2'=>'用户'],['prompt'=>'全部'])  ?>

    <?= $form->field($model, 'type')->dropDownList(Dics::getAccountType(),['prompt'=>'全部'])  ?>

    <?= $form->field($model, 'start') ?>

    <?=$form->field($model, 'end') ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary','style'=>'margin-top:-8px']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-warning','style'=>'margin-top:-8px']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
