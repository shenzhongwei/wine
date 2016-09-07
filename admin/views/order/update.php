<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\OrderInfo $model
 */

$this->title = 'Update Order Info: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-info-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
