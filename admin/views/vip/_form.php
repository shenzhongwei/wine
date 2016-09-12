<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodVip $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="good-vip-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'gid'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 商品id...']],

            'limit'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 限购数量...']],

            'is_active'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 是否上架...']],

            'price'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 会员专享价...', 'maxlength'=>10]],

        ]

    ]);

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
