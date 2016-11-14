<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model admin\models\AuthItem */

$this->title = '更新管理员';
$user = Yii::$app->user->identity;
?>
<div>
    <div class="ibox-content">
        <div class="row pd-10">
            <h1><?= Html::encode($this->title) ?></h1>
            <div class="auth-item-form col-sm-4">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'wa_id')->hiddenInput()->label('')?>

                <?= $form->field($model, 'wa_username')->textInput(['readonly'=>true,'disabled'=>true])->label('用户名<font style=" font-size: 12px;color: #28866a">（不可修改）</font>') ?>
                <?php
                if(($user->wa_type<$model->wa_type) || $user->wa_id == $model->wa_id){
                    echo $form->field($model, 'wa_password')->passwordInput(['value'=>'******','onchange'=>"$('#confirm_password').show()"])->label('密码');
                    ?>
                    <div id="confirm_password" style="display: none">
                        <?= $form->field($model, 'confirm_password')->passwordInput(['value'=>''])->label('确认密码')?>
                    </div>
                <?php
                }else{
                    echo $form->field($model, 'wa_password')->passwordInput(['value'=>'******','disabled'=>true,'readonly'=>true])->label('密码<font style=" font-size: 12px;color: #28866a">（不可修改）</font>');
                ?>
                    <div id="confirm_password" style="display: none">
                        <?= $form->field($model, 'confirm_password')->passwordInput(['value'=>''])->label('确认密码')?>
                    </div>
                <?php
                }
                echo $form->field($model, 'wa_phone')->textInput(['onkeyup'=>'this.value=this.value.replace(/\D/gi,"")'])->label('手机号')?>
                <?= $form->field($model, 'wa_name')->textInput()->label('姓名')?>
                <?php
                if($user->wa_type<$model->wa_type){
                    echo $form->field($model->admingroup, 'item_name' )->dropDownList($item,['disabled'=>true])->label('用户组');
                }else{
                    echo $form->field($model->admingroup, 'item_name' )->dropDownList($item,['disabled'=>true])->label('用户组<font style=" font-size: 12px;color: #28866a">（不可修改）</font>');
                }
                ?>
                <?php ActiveForm::end(); ?>
                    <button type="submit" id="manager_update" class="btn btn-primary">保存</button>
            </div>
        </div>
    </div>

</div>

<?=Html::jsFile('@web/js/wine/manager.js?_'.time())?>
