<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use admin\models\GoodCountry;
use admin\models\Country;
/**
 * @var yii\web\View $this
 * @var admin\models\GoodCountry $model
 */
?>
<div class="good-country-create">
    <div class="good-country-form">

        <?php $form = ActiveForm::begin([
            'id' => 'country-form',
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableAjaxValidation' => true, //开启ajax验证
            'validationUrl' => Url::toRoute(['valid-form', 'key' => 'country']), //验证url
            'action' => Url::toRoute(['type/child-create', 'key' => 'country', 'type' => $model->type]),
        ]);
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'name' => ['type' => Form::INPUT_WIDGET, 'widgetClass'=>\kartik\select2\Select2::className(),'options'=>[
                    'data' => Country::GetAllCountry(),
                    'options' => ['placeholder' => '请选择国家'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]],
                'type'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>\kartik\select2\Select2::className(),'options'=>[
                    'data' => GoodCountry::GetAllTypes(),
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
