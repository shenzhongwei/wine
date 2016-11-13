<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\ShopInfo $model
 */

$this->title = ' 新添门店';
$this->params['breadcrumbs'][] = ['label' => '门店列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-info-create">
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
