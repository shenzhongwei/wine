<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use admin\models\GoodColor;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodSmell $model
 */
?>
<div class="good-color-create">
    <div class="good-color-form">

        <?php $form = ActiveForm::begin([
            'id' => 'color-form',
            'type' => ActiveForm::TYPE_VERTICAL,
            'action' => Url::toRoute(['type/child-create', 'key' => 'color','type' => $model->type]),
            'enableAjaxValidation' => true, //开启ajax验证
            'validationUrl' => Url::toRoute(['valid-form', 'key' => 'color']), //验证url
        ]);
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'name' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => '请输入名称', 'maxlength' => 25]],
                'type' => ['type' => Form::INPUT_DROPDOWN_LIST, 'items' => GoodColor::GetAllTypes(), 'options' => [
                    'readonly' => true,
                    'options' => ['placeholder' => '请选择类型'],
                ],],
            ]
        ]);
        echo Html::submitButton('保存', ['class' =>'btn btn-success']);
        ActiveForm::end(); ?>

    </div>
</div>
