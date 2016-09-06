<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\MerchantInfo $model
 */

$this->title = 'Create Merchant Info';
$this->params['breadcrumbs'][] = ['label' => 'Merchant Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="merchant-info-create">

    <?= $this->render('_form', [
        'model' => $model,
        'item_arr'=>$item_arr
    ]) ?>

</div>
