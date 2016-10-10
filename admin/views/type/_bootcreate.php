<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\Url;
use admin\models\GoodBoot;
use admin\models\Zone;
use common\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodBoot $model
 */
?>
<div class="good-boot-create">
    <div class="good-boot-form">

        <?php $form = ActiveForm::begin([
            'id' => 'boot-form',
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableAjaxValidation' => true, //开启ajax验证
            'validationUrl' => Url::toRoute(['valid-form', 'key' => 'boot']), //验证url
            'action' => Url::toRoute(['type/child-create', 'key' => 'boot' ,'type' => $model->type]),
        ]);
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'name' => ['type' => Form::INPUT_WIDGET, 'widgetClass'=>\kartik\select2\Select2::className(),'options'=>[
                    'data' => ArrayHelper::map(Zone::GetAllProvince(),'shortname','shortname'),
                    'options' => ['placeholder' => '请选择产地'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]],
                'type'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>\kartik\select2\Select2::className(),'options'=>[
                    'data' => GoodBoot::GetAllTypes(),
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
