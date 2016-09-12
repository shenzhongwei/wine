<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use kartik\widgets\Select2;
use admin\models\EmployeeInfo;
/**
 * @var yii\web\View $this
 * @var admin\models\EmployeeInfoSearch $model
 * @var yii\widgets\ActiveForm $form
 */

//var_dump($model->owner_id);
//exit;
?>

<div class="employee-info-search">

    <?php $form =ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options'=>[
            'class'=>'form-inline',
        ],
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'phone') ?>

    <?= $form->field($model, 'type')->dropDownList(['0'=>'商户','1'=>'门店'],['prompt'=>'全部','id'=>'employee_search_type']) ?>

    <?= $form->field($model, 'owner_id')->widget(DepDrop::className(),[
            'data'=>$model->owner_id==='' ? [] : EmployeeInfo::getOwners($model->type),
            'options'=>[ 'placeholder'=>'请选择所属商家/门店'],
            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
            'pluginOptions'=>[
                'placeholder'=>'',
                'depends'=>['employee_search_type'],
                'url' =>Url::to(['employ/owners']),
                'loadingText' => '查找...',
            ]
    ])->label('所属商家/门店名称')?>

    <?=$form->field($model, 'status')->dropDownList(['0'=>'已删','1'=>'正常','2'=>'繁忙','3'=>'下岗'],['prompt'=>'全部']) ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary','style'=>'margin-top:-8px']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default','style'=>'margin-top:-8px']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?=Html::a('<i class="glyphicon glyphicon-plus"></i>新增配送人员', ['create'], ['class' => 'btn btn-success'])?>