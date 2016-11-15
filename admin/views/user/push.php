<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\builder\Form;
/**
 * @var yii\web\View $this
 * @var admin\models\Push $model
 */

$this->title = ' 发布推送';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-rush-create">
    <div class="panel panel-success">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-send"></span><?= $this->title ?>
        </div>
        <div class="panel-body">
            <div class="push-form" style="width: 60%">
                <?php $form = ActiveForm::begin([
                    'type'=>ActiveForm::TYPE_VERTICAL,
                    'formConfig' => [
                        'deviceSize' => ActiveForm::SIZE_LARGE,
                    ],
                ]);
                ?>

                <?php

                echo Form::widget([

                    'model' => $model,
                    'form' => $form,
                    'columns' => 1,
                    'attributes' => [
                        'content'=>['type'=>Form::INPUT_TEXTAREA,'options'=>[
                            'style'=>'min-height:200px'
                        ]],
                    ]

                ]);

                echo Html::submitButton(Yii::t('app', '发 送') , ['class' =>'btn btn-success']);
                ?>

                <?php
                ActiveForm::end(); ?>
            </div>
        </div></div>
</div>
