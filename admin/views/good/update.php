<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodInfo $model
 */

$this->title = '编辑产品: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Good Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="good-info-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
