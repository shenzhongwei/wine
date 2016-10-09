<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use admin\models\GoodSmell;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodSmell $model
 */
?>
<div class="good-smell-create">
    <div class="good-smell-form">

        <?php $form = ActiveForm::begin([
            'id' => 'smell-form',
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableAjaxValidation' => true, //开启ajax验证
            'validationUrl' => Url::toRoute(['valid-form', 'key' => 'smell']), //验证url
            'action' => Url::toRoute(['type/child-create', 'key' => 'smell']),
        ]);
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'name' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => '请输入名称', 'maxlength' => 25]],
                'type' => ['type' => Form::INPUT_DROPDOWN_LIST, 'items' => GoodSmell::GetAllTypes(), 'options' => [
                    'readonly' => true,
                    'options' => ['placeholder' => '请选择类型'],
                ],],
            ]
        ]);
        echo Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
        ActiveForm::end(); ?>

    </div>
</div>
