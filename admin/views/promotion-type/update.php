<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\PromotionType $model
 */

$this->title = ' 编辑分类: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '优惠券分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="promotion-type-update">
    <div class="panel panel-success">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-edit"></span><?= $this->title ?>
        </div>
        <div class="panel-body">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
            </div></div>
</div>
