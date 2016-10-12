<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use admin\models\GoodPriceField;
/**
 * @var yii\web\View $this
 * @var admin\models\GoodPriceField $model
 */
?>
<div class="good-price-update">
    <div class="good-price-form">

        <?php $form = ActiveForm::begin([
            'id' => 'price-form',
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableAjaxValidation' => true, //开启ajax验证
            'validationUrl' => Url::toRoute(['valid-form', 'key' => 'price']), //验证url
            'action' => Url::toRoute(['type/price-update',  'type' => $model->type,'id'=>$model->id]),
        ]);
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'id' => ['type'=>Form::INPUT_HIDDEN,'label'=>false],
                'start' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => '请输入起始金额','maxlength'=>10,'onkeyup'=>'this.value=this.value.replace(/\D/gi,"")']],
                'end_rep' => ['type' => Form::INPUT_TEXT, 'options' => ['value'=>$model->end=='+∞' ? '':$model->end,'placeholder' => '请输入终止金额(若要设置无限大，则无需输入)','maxlength'=>10,'onkeyup'=>'this.value=this.value.replace(/\D/gi,"")']],
                'end' => ['type' => Form::INPUT_HIDDEN, 'options' => ['placeholder' => '请输入终止金额(若要设置无限大，则无需输入)'],'label'=>false],
                'type'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>\kartik\select2\Select2::className(),'options'=>[
                    'data' => GoodPriceField::GetAllTypes(),
                    'options' => ['placeholder' => '请选择类型'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],],
            ]
        ]);
        echo Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
        ActiveForm::end(); ?>

    </div>
</div>
<script>
    $(function () {
        $('#goodpricefield-end_rep').change(function () {
            end = $(this).val();
            if(end==''){
                $('#goodpricefield-end').val('+∞');
            }else{
                $('#goodpricefield-end').val(end);
            }
        });
    })
</script>
