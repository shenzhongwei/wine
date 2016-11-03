<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use yii\helpers\Url;
use kartik\widgets\Select2;
use admin\models\GoodVip;

/**
 * @var yii\web\View $this
 * @var admin\models\HotSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="good-vip-form" style="width: 80%">
        <?php $form = ActiveForm::begin([
                'type'=>ActiveForm::TYPE_VERTICAL,
                'formConfig' => [
                    'deviceSize' => ActiveForm::SIZE_LARGE,
                ],
                'enableAjaxValidation'=>true, //开启ajax验证
                'validationUrl'=>Url::toRoute(['valid-form']), //验证url
            ]
        );
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [

                'name'=>['type'=> Form::INPUT_TEXT,'options'=>['placeholder'=>'请填写热搜名','maxlength'=>20]],

                'order'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'填写热搜排名（1~5）', 'maxlength'=>1,'onkeyup'=>'this.value=this.value.replace(/\D/gi,"")']],

            ]

        ]);
        ?>
            <?php
            echo Html::submitButton( Yii::t('app', 'Save'), ['class' =>'btn btn-success']);
            ActiveForm::end(); ?>
</div>
