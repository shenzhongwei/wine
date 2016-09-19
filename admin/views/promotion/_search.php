<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
USE admin\models\Dics;
use yii\helpers\ArrayHelper;
use admin\models\PromotionInfo;
/**
 * @var yii\web\View $this
 * @var admin\models\PromotionInfoSearch $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="promotion-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options'=>[
            'class'=>'form-inline',
        ],
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['placeholder'=>'例：充值优惠']) ?>

    <?= $form->field($model, 'condition')->textInput(['placeholder'=>'例：500','style'=>['width'=>'100px']]) ?>

    <?= $form->field($model, 'valid_circle')->dropDownList(PromotionInfo::getValidRange(),['prompt'=>'全部']) ?>

    <?= $form->field($model, 'limit')->dropDownList(Dics::getPromotionRange(),['prompt'=>'全部'])  ?>

    <?= $form->field($model, 'start_from')->widget(DatePicker::classname(),[
        'options' => ['placeholder' => date('Y-m-d'),'style'=>['width'=>'120px']],
        'pluginOptions' => [
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ]) ?>

    <?= $form->field($model, 'end_to')->widget(DatePicker::classname(),[
        'options' => ['placeholder' =>date('Y-m-d'),'style'=>['width'=>'120px']],
        'pluginOptions' => [
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ]) ?>

    <?= $form->field($model, 'is_active')->dropDownList(['0'=>'否','1'=>'是'],['prompt'=>'全部']) ?>


    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary','style'=>'margin-top:-8px']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-warning','style'=>'margin-top:-8px']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?=Html::a('<i class="glyphicon glyphicon-plus"></i>新增优惠促销活动', ['create'], ['class' => 'btn btn-success','style'=>'margin-top:5px;'])?>