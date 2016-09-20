<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var admin\models\MessageSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="message-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options'=>[
            'class'=>'form-inline',
        ]
    ]); ?>

    <?= $form->field($model, 'type_id')->dropDownList(\admin\models\Dics::getMessageType(),['prompt'=>'请选择消息类型','id'=>'message_search_type']) ?>

    <?= $form->field($model, 'own_id')->widget(\kartik\widgets\DepDrop::className(),[
        'data'=> $model->own_id==='' ? [] : \admin\models\MessageList::getacceptName($model->type_id),
        'options'=>[ 'placeholder'=>'请选择对应消息类型的名称'],
        'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
        'pluginOptions'=>[
            'placeholder'=>'请选择对应消息类型的名称',
            'depends'=>['message_search_type'],
            'url' =>\yii\helpers\Url::to(['message/relation-name']),
            'loadingText' => '查找...',
        ]
    ])?>
    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'target')->dropDownList(\admin\models\Dics::getMessageToUrl(),['prompt'=>'选择消息跳转页面']) ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary','style'=>'margin-top:-8px']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-warning','style'=>'margin-top:-8px']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
