<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\PromotionType $model
 */

$this->title = '更改类别: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '优惠券分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="promotion-type-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
