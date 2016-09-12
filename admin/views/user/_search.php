<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use admin\models\UserInfo;
use yii\helpers\ArrayHelper;
/**
 * @var yii\web\View $this
 * @var admin\models\UserInfoSearch $model
 * @var yii\widgets\ActiveForm $form
 */

$usermodel=UserInfo::find()->select(['phone'])->asArray()->all();
$username=ArrayHelper::getColumn($usermodel,function($element){
    return  $element['phone'];
});
//var_dump($username);exit;
?>

<div class="user-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options'=>[
            'class'=>'form-inline',
        ]
    ]); ?>

    <?= $form->field($model, 'phone')->widget(AutoComplete::className(),[
        'clientOptions' => [
            'source' =>$username,
        ],
    ])->textInput() ?>

    <?=$form->field($model, 'name')->textInput(['options'=>['placeholder'=>'昵称/真实姓名']])->label('用户名') ?>

    <?=$form->field($model, 'invite_user')->label('邀请人') ?>

    <?=$form->field($model, 'invite_code')?>

    <?=$form->field($model, 'is_vip')->dropDownList(['0'=>'否','1'=>'是'],['prompt'=>'全部']) ?>

    <?=$form->field($model, 'status')->dropDownList(['0'=>'删除','1'=>'正常'],['prompt'=>'全部']) ?>


    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary','style'=>'margin-top:-8px']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default','style'=>'margin-top:-8px']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?=Html::a('<i class="glyphicon glyphicon-plus"></i>新增用户', ['create'], ['class' => 'btn btn-success'])?>
