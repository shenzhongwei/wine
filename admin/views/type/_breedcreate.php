<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use admin\models\GoodBreed;
/**
 * @var yii\web\View $this
 * @var admin\models\GoodBreed $model
 */
?>
<div class="good-breed-create">
    <div class="good-breed-form">

        <?php $form = ActiveForm::begin([
            'id' => 'breed-form',
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableAjaxValidation' => true, //开启ajax验证
            'validationUrl' => Url::toRoute(['valid-form', 'key' => 'breed']), //验证url
            'action' => Url::toRoute(['type/child-create', 'key' => 'breed', 'type' => $model->type]),
        ]);
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'name' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => '请输入品种名称', 'maxlength' => 25]],
                'type'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>\kartik\select2\Select2::className(),'options'=>[
                    'data' => GoodBreed::GetAllTypes(),
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
