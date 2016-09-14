<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\PromotionInfo $model
 */

$this->title = '更新活动信息: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '活动促销列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="promotion-info-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
