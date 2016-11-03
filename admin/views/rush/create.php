<?php

use yii\helpers\Html;
/**
 * @var yii\web\View $this
 * @var admin\models\GoodRush $model
 */

$this->title = ' 发布抢购';
$this->params['breadcrumbs'][] = ['label' => '抢购管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-rush-create">
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
