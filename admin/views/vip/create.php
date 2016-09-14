<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodVip $model
 */

$this->title = '发布会员产品';
$this->params['breadcrumbs'][] = ['label' => '会员产品', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-vip-create">
    <h2><?= Html::encode($this->title) ?></h2>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
