<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\MerchantInfo $model
 */

$this->title = 'Update Merchant Info: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Merchant Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="merchant-info-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
