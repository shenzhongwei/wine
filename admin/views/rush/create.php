<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodRush $model
 */

$this->title = 'Create Good Rush';
$this->params['breadcrumbs'][] = ['label' => 'Good Rushes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-rush-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
