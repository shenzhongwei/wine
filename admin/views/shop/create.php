<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\ShopInfo $model
 */

$this->title = '新添门店';
$this->params['breadcrumbs'][] = ['label' => '门店列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-info-create">
    <?= $this->render('_form', [
        'model' => $model,
        'item_arr'=>$item_arr,
        'p1'=>$p1,'p2'=>$p2,
        'PreviewConfig' =>$PreviewConfig,

        'province'=>$province,
        'city'=>$city,
        'district'=>$district
    ]) ?>

</div>
