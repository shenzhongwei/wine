<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\AdList $model
 */

$this->title = '新增广告';
$this->params['breadcrumbs'][] = ['label' =>'广告管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
