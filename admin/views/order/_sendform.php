<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use kartik\select2\Select2;
use admin\models\Dics;
use kartik\file\FileInput;
use kartik\depdrop\DepDrop;
use admin\models\AdList;
/**
 * @var yii\web\View $this
 * @var admin\models\OrderSend $model
 * @var yii\widgets\ActiveForm $form
 */

?>
<style>
    .select2-dropdown--below{
        z-index: 99999;
    }
</style>
<div class="send-list-form">

    <?php $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_VERTICAL,
        'id'=>'send-form',
        'formConfig' => [
            'deviceSize' => ActiveForm::SIZE_SMALL,
        ],
    ]);
    ?>

    <?php

    echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [
            ''
        ]

    ]);

    echo Html::submitButton(Yii::t('app', 'Save') , ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ?>

    <?php
    ActiveForm::end(); ?>
</div>
