<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\AdList $model
 */

$this->title = '新增广告';
$this->params['breadcrumbs'][] = ['label' => 'Ad Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ad-list-create" style="margin-top: 10px;">

    <?= $this->render('_form', [
        'model' => $model,
        'p1'=>$p1,
        'P'=>$P
    ]) ?>

</div>
