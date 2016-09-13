<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\PromotionInfo $model
 */

$this->title = 'Create Promotion Info';
$this->params['breadcrumbs'][] = ['label' => 'Promotion Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promotion-info-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
