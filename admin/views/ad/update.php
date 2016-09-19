<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\AdList $model
 */

$this->title = '更新广告: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ad Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ad-list-update">

    <?= $this->render('_form', [
        'model' => $model,
        'p1'=>$p1,
        'P'=>$P
    ]) ?>

</div>
