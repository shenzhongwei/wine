<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodInfo $model
 */

$this->title = ' 新增商品';
$this->params['breadcrumbs'][] = ['label' => 'Good Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-info-create">
    <div class="panel panel-success">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-plus"></span><?= $this->title ?>
        </div>
        <div class="panel-body">
<!--    <div class="row">-->
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
<!--    </div>-->
            </div>
        </div>
</div>
