<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\MerchantInfo $model
 */

$this->title = ' 更新商户: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '商户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="merchant-info-update">

    <div class="panel panel-success">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-edit"></span><?= $this->title ?>
        </div>
        <div class="panel-body">
            <!--    <div class="row">-->
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
            <!--    </div>-->
        </div>
    </div>

</div>
