<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodBrand $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Good Brand',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Good Brands'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-brand-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
