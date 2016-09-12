<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodVip $model
 */

$this->title = 'Create Good Vip';
$this->params['breadcrumbs'][] = ['label' => 'Good Vips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-vip-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
