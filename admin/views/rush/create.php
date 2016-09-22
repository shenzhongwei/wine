<?php

use yii\helpers\Html;
/**
 * @var yii\web\View $this
 * @var admin\models\GoodRush $model
 */

$this->title = '发布抢购';
$this->params['breadcrumbs'][] = ['label' => '抢购管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-rush-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
