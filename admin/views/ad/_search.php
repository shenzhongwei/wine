<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use admin\models\Dics;
/**
 * @var yii\web\View $this
 * @var admin\models\AdListSearch $model
 * @var yii\widgets\ActiveForm $form
 */
    $model->is_show=1;
?>

<div class="ad-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options'=>[
            'class'=>'form-inline',
        ]
    ]); ?>

    <?= $form->field($model, 'type')->dropDownList(Dics::getPicType(),['prompt'=>'全部','id'=>'pic_search_type']) ?>

    <?= $form->field($model, 'target_name')->widget(\kartik\widgets\DepDrop::className(),[
        'data'=> $model->target_name==='' ? [] : \admin\models\AdList::getacceptName($model->type),
        'options'=>[ 'placeholder'=>'请选择对应类型的名称'],
        'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
        'pluginOptions'=>[
            'placeholder'=>'请选择对应类型的名称',
            'depends'=>['pic_search_type'],
            'url' =>\yii\helpers\Url::to(['ad/relation-name']),
            'loadingText' => '查找...',
        ]
    ])?>

    <?= $form->field($model, 'is_show')->radioList([0=>'不显示',1=>'显示'],['class'=>'form-control']) ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary','style'=>'margin-top:-8px']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-warning','style'=>'margin-top:-8px']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
