<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodVip $model
 */

$this->title = ' 发布热搜';
$this->params['breadcrumbs'][] = ['label' => '热搜列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-vip-create">
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
