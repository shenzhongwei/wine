<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '双天酒后台管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<title><?= Html::encode($this->title) ?></title>
<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        <div>

            <h1 class="logo-name" style="font-size: 80px;text-align: center;letter-spacing: 0px;margin-top: 200px">SANTE</h1>

        </div>
        <h3></h3>


    <div class="row">
        <div class="">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'wa_username')->textInput(['autofocus' => true])->label('用户名') ?>

                <?= $form->field($model, 'wa_password')->passwordInput()->label('密码') ?>
            <div class="form-group field-admin-rememberme">
                <div class="checkbox">
                    <label for="admin-rememberme">
                        <input name="Admin[rememberMe]" value="0" type="hidden">
                        <input id="admin-rememberme" name="Admin[rememberMe]" style="margin-top: 2px" value="1" checked="" type="checkbox">记住密码？
                    </label>
                    <p class="help-block help-block-error"></p>
                </div>
            </div>
                <div class="form-group">
                    <?= Html::submitButton('登录', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    </div>
    <div class="footer">
    </div>
</div>



