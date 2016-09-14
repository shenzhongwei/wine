<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\PromotionInfo $model
 */

$this->title = '新增活动';
$this->params['breadcrumbs'][] = ['label' => '活动促销列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promotion-info-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
