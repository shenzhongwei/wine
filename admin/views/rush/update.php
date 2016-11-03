<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodRush $model
 */

$this->title = ' 编辑抢购'  . $model->g->name;
$this->params['breadcrumbs'][] = ['label' => '抢购管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="good-rush-update">
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
