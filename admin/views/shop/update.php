<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\ShopInfo $model
 */

$this->title = ' 更新门店信息:'. $model->name;
$this->params['breadcrumbs'][] = ['label' => '门店列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shop-info-update">

    <div class="panel panel-success">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-edit"></span><?= $this->title ?>
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
