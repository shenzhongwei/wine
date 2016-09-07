<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\OrderInfo $model
 */

$this->title = 'Create Order Info';
$this->params['breadcrumbs'][] = ['label' => 'Order Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-info-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
