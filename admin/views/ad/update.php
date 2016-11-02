<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\AdList $model
 */

$this->title = '更新广告';
$this->params['breadcrumbs'][] = ['label' => '广告管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
