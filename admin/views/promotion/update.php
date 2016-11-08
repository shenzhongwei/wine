<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\PromotionInfo $model
 * @var string $is_time
 * @var string $is_ticket
 */

$this->title = ' 更新活动: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '活动促销列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="promotion-info-update">
    <div class="panel panel-success">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-edit"></span><?= $this->title ?>
        </div>
        <div class="panel-body">
    <?= $this->render('_form', [
        'model' => $model
    ]) ?>
        </div>
    </div>
</div>
