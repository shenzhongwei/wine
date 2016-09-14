<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodVip $model
 */

$this->title = '会员产品更新: ' . ' ' . $model->g->name;
$this->params['breadcrumbs'][] = ['label' => 'Good Vips', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="good-vip-update">
    <div class="ibox-content">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
        </div>
</div>
