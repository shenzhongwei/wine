<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodVip $model
 */

$this->title = '发布热搜';
$this->params['breadcrumbs'][] = ['label' => '热搜列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-vip-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
