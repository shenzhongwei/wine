<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\PromotionType $model
 */

$this->title = ' 新增分类';
$this->params['breadcrumbs'][] = ['label' => '优惠券分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promotion-type-create">
    <div class="panel panel-success">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-plus"></span><?= $this->title ?>
        </div>
        <div class="panel-body">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div></div>
</div>
