<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodInfo $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="ibox-content good-info-form">

    <?php $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_HORIZONTAL,
        'fullSpan'=>6,
        'formConfig' => [
            'labelSpan' => 1,
            'deviceSize' => ActiveForm::SIZE_SMALL,
        ]
    ]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'columnSize'=>'sm',
        'attributes' => [
            'merchant'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 所属商户...']],

            'type'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 类型...','']],

            'brand'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 品牌...']],

            'smell'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 香型...']],

            'color'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 颜色类型...']],

            'dry'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 干型id...']],

            'boot'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 产地...']],

            'breed'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 品种...']],

            'country'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 国家...']],

            'style'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 类型...']],

            'order'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 排序...']],

            'regist_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 添加时间...']],

            'is_active'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 是否上架...']],

            'active_at'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 上架状态更改时间...']],

            'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 商品名...', 'maxlength'=>50]],

            'detail'=>['type'=> Form::INPUT_TEXTAREA, 'options'=>['placeholder'=>'Enter 详情...','rows'=> 6]],

            'price'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 价格...', 'maxlength'=>10]],

            'volum'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 容量...', 'maxlength'=>128]],

            'unit'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 单位...', 'maxlength'=>10]],

            'pic'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 图片...', 'maxlength'=>128]],

            'number'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter 编号...', 'maxlength'=>8]],

        ]

    ]);

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', '保存') : Yii::t('app', '保存'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
