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
 * @var admin\models\GoodVip $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="good-vip-form">
    <div class="panel panel-info" style="width: 70%">
        <div class="panel-heading">
            <?= $model->isNewRecord ? '发布会员商品' : '编辑会员商品' ?>
        </div>
        <div class="panel-body">
        <?php $form = ActiveForm::begin([
                'type'=>ActiveForm::TYPE_VERTICAL,
                'formConfig' => [
                    'deviceSize' => ActiveForm::SIZE_LARGE,
                ],
                'enableAjaxValidation'=>true, //开启ajax验证
                'validationUrl'=>Url::toRoute(['valid-form','id'=>empty($model['id'])?0:$model['id']]), //验证url
            ]
        );
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'id'=>['type'=>Form::INPUT_HIDDEN,'label'=>false],

                'gid'=>['label'=>'商品','type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                    'options'=>[
                        'data'=>GoodVip::GetGoods(),
                        'options'=>['placeholder'=>'请选择商品'],
                        'pluginOptions' => ['allowClear' => true],
                    ],
                ],

                'price'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'请设置会员价', 'maxlength'=>10,'onkeyup'=>'clearNoNum(this)']],

            ]

        ]);
        ?>
            <?php
            echo Html::submitButton( Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
            ActiveForm::end(); ?>
            </div>
        </div>
</div>
