<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\ShopInfo $model
 */

$this->title = '更新门店信息' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '门店列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shop-info-update">

    <?= $this->render('_form', [
        'model' => $model,
        'p1' =>$p1, 'p2' =>$p2,
        // 需要展示的图片设置，比如图片的宽度等s
        'PreviewConfig' =>$PreviewConfig,
    ]) ?>

</div>
