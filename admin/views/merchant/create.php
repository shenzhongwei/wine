<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\MerchantInfo $model
 */

$this->title = '新添商户';
$this->params['breadcrumbs'][] = ['label' => 'Merchant Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="merchant-info-create">

    <?= $this->render('_form', [
        'model' => $model,
        'item_arr'=>$item_arr,
        'province'=>$province,
        'city'=>$city,
        'district'=>$district,
    ]) ?>

</div>
