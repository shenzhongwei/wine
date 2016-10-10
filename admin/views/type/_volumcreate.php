<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use admin\models\GoodModel;
/**
 * @var yii\web\View $this
 * @var admin\models\GoodSmell $model
 */
?>
<div class="good-volum-create">
    <div class="good-volum-form">

        <?php $form = ActiveForm::begin([
            'id' => 'volum-form',
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableAjaxValidation' => true, //开启ajax验证
            'validationUrl' => Url::toRoute(['valid-form', 'key' => 'volum']), //验证url
            'action' => Url::toRoute(['type/child-create', 'key' => 'volum', 'type' => $model->type]),
        ]);
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'name' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => '规格(单位：mL;L等)', 'maxlength' => 25]],
                'type'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>\kartik\select2\Select2::className(),'options'=>[
                    'data' => GoodModel::GetAllTypes(),
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
