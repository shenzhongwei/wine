<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use admin\models\GoodType;
use admin\models\MerchantInfo;
use yii\jui\AutoComplete;
use admin\models\GoodInfo;
use kartik\widgets\Select2;

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

    <?php  echo $form->field($model, 'name',[
        'options'=>['class'=>'col-sm-2','style'=>'padding:0;width:auto']])->widget(AutoComplete::className(),[
        'clientOptions' => [
                'source' =>GoodInfo::GetGoodNames(),
      ],
    ])->textInput() ?>

<!--    --><?php // echo $form->field($model, 'type')->dropDownList(GoodType::GetTypes(),['prompt'=>'请选择类型','style'=>'margin-right:10px']) ?>

    <?=$form->field($model,'type',[
        'options'=>['class'=>'col-sm-2','style'=>'padding:0;width:auto'],
        'template' => "{label}\n<div class='col-sm-9'>{input}</div>",
        'labelOptions' => ['class' => 'col-lg-2'],
    ])->widget(Select2::className(),[
        'data'=>GoodType::GetTypes(),
        'options'=>['placeholder'=>'请选择商品大类'],
        'pluginOptions' => ['allowClear' => true],
    ]) ?>

    <?=$form->field($model,'merchant',[
        'options'=>['class'=>'col-sm-2','style'=>'padding:0;width:auto'],
        'template' => "{label}\n<div class='col-sm-9'>{input}</div>",
        'labelOptions' => ['class' => 'col-lg-2'],
    ])->widget(Select2::className(),[
        'data'=>MerchantInfo::GetMerchants(),
        'options'=>['placeholder'=>'请选择商户'],
        'pluginOptions' => ['allowClear' => true],
    ])->label('商户') ?>

<!--    --><?php // echo $form->field($model, 'merchant')->dropDownList(MerchantInfo::GetMerchants(),['prompt'=>'请选择商户','style'=>'margin-right:10px']) ?>

    <?php  echo $form->field($model, 'volum')->textInput(['style'=>'margin-right:10px']) ?>

    <?php  echo $form->field($model, 'price')->hiddenInput() ?>

    <?php  echo $form->field($model, 'start_price')->textInput(['style'=>'width:80px','onkeyup'=>'this.value=this.value.replace(/\D/gi,"")'])->label(false) ?>
至
    <?php  echo $form->field($model, 'end_price')->textInput(['style'=>'margin-right:10px;width:80px','onkeyup'=>'this.value=this.value.replace(/\D/gi,"")'])->label(false) ?>

    <?php  echo $form->field($model, 'number')->widget(AutoComplete::className(),[
        'clientOptions' => [
            'source' =>GoodInfo::GetGoodNumbers(),
        ]
    ])->textInput(['style'=>'margin-right:10px']) ?>

    <?php  echo $form->field($model, 'is_active')->radioList([0=>'未上架',1=>'上架中'],['style'=>'margin-right:10px']) ->label(false) ?>

    <div class="form-group" style="height: 44px">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
<?php
echo Html::a('<i class="glyphicon glyphicon-plus"></i> 发布商品', ['create'], ['class' => 'btn btn-success']);
?>
