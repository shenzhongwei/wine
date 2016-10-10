<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use admin\models\GoodColor;
/**
 * @var yii\web\View $this
 * @var admin\models\GoodColor $model
 */
?>
<div class="good-color-create">
    <div class="good-color-form">

        <?php $form = ActiveForm::begin([
            'id' => 'color-form',
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableAjaxValidation' => true, //开启ajax验证
            'validationUrl' => Url::toRoute(['valid-form', 'key' => 'color']), //验证url
            'action' => Url::toRoute(['type/child-create', 'key' => 'color', 'type' => $model->type]),
        ]);
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'name' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => '请输入色型名称', 'maxlength' => 25]],
                'type'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>\kartik\select2\Select2::className(),'options'=>[
                    'data' => GoodColor::GetAllTypes(),
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
