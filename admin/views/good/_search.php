<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use admin\models\GoodType;
use admin\models\MerchantInfo;
use kartik\widgets\Select2;
use kartik\builder\Form;

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
        'type'=>ActiveForm::TYPE_INLINE,
        'fullSpan'=>12,
        'formConfig' => [
            'deviceSize' => ActiveForm::SIZE_SMALL,
        ],
    ]); ?>
    <?php
    echo Form::widget([
        'model' => $model,
        'form' => $form,
        'columns' => 7,
        'columnSize'=>'sm',
        'attributes' => [
            'name'=>[
                'type'=> Form::INPUT_TEXT, 'options'=>[['style'=>'margin-right:10px']]
            ],
            'type'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                'options'=>[
                    'data'=>GoodType::GetTypes(),
                    'options'=>['placeholder'=>'请选择大类'],
                    'pluginOptions' => ['allowClear' => false],
                ]
            ],
            'merchant'=>[
                'type'=> Form::INPUT_WIDGET,'widgetClass'=>Select2::className(),
                'options'=>[
                    'data'=>MerchantInfo::GetMerchants(),
                    'options'=>['placeholder'=>'请选择商户'],
                    'pluginOptions' => ['allowClear' => false],
                ]
            ]
        ]

    ]);
    ?>

<!--    --><?php // echo $form->field($model, 'type')->dropDownList(GoodType::GetTypes(),['prompt'=>'请选择类型','style'=>'margin-right:10px']) ?>
<!--    --><?php // echo $form->field($model, 'type')->widget(Select2::className(),[
//            'data'=>GoodType::GetTypes(),
//            'options'=>['placeholder'=>'请选择商品大类'],
//            'pluginOptions' => ['allowClear' => true],
//        'class'=>'form-inline'
//    ]) ?>

<!--    --><?php // echo $form->field($model, 'merchant')->dropDownList(MerchantInfo::GetMerchants(),['prompt'=>'请选择商户','style'=>'margin-right:10px']) ?>
<!---->
<!--    --><?php // echo $form->field($model, 'volum')->textInput(['style'=>'margin-right:10px']) ?>
<!---->
<!--    --><?php // echo $form->field($model, 'price')->hiddenInput() ?>
<!---->
<!--    --><?php // echo $form->field($model, 'start_price')->textInput(['style'=>'width:80px','onkeyup'=>'this.value=this.value.replace(/\D/gi,"")'])->label(false) ?>
<!--至-->
<!--    --><?php // echo $form->field($model, 'end_price')->textInput(['style'=>'margin-right:10px;width:80px','onkeyup'=>'this.value=this.value.replace(/\D/gi,"")'])->label(false) ?>
<!---->
<!--    --><?php // echo $form->field($model, 'number')->textInput(['style'=>'margin-right:10px']) ?>
<!---->
<!--    --><?php // echo $form->field($model, 'is_active')->radioList([0=>'未上架',1=>'上架中'],['style'=>'margin-right:10px']) ->label(false) ?>

    <div class="form-group" style="height: 44px">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
<?php
echo Html::a('<i class="glyphicon glyphicon-plus"></i> 发布商品', ['create'], ['class' => 'btn btn-success']);
?>
