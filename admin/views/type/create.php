<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodType $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Good Type',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Good Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-type-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
