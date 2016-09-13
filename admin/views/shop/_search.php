<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use admin\models\Zone;
use yii\helpers\Url;
use yii\jui\AutoComplete;
/**
 * @var yii\web\View $this
 * @var admin\models\ShopSearch $model
 * @var yii\widgets\ActiveForm $form
 * //省
 *  */
$province=ArrayHelper::map(Zone::getProvince(),'id','name');
$city=[];
$district=[];
?>

<script type="text/javascript">
    $(document).ready(function() {
        selectAddress($("#shopsearch-province"),$("#shopsearch-city"),$("#shopsearch-district"));
    });
</script>

<script type="text/javascript" src="<?=Url::to('@web/js/wine/address.js') ?>"></script>

<div class="shop-info-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options'=>[
            'class'=>'form-inline',
        ],
    ]); ?>

    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'merchant_name')->widget(AutoComplete::className(),[
        'clientOptions' => [
            'source' =>$mername,
        ]
    ])->textInput() ?>
    <?=$form->field($model, 'limit') ?>
    <?=$form->field($model, 'least_money') ?>
    &nbsp;&nbsp;&nbsp;
    <?= $form->field($model, 'province')->dropDownList($province,['prompt'=>'--省--'])->label(false)?>
    <?= $form->field($model, 'city')->dropDownList($city,['prompt'=>'--市--','disabled'=>true])->label(false)?>
    <?= $form->field($model, 'district')->dropDownList($district,['prompt'=>'--区--','disabled'=>true])->label(false)?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary','style'=>'margin-top:-8px']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-warning','style'=>'margin-top:-8px']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?=Html::a('<i class="glyphicon glyphicon-plus"></i>新增门店', ['create'], ['class' => 'btn btn-success'])?>
