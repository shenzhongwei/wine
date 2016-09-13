<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\UserInfo $model
 */

$this->title = '更新用户信息: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-info-update">

    <?= $this->render('_form', [
        'model' => $model,
        'p1'=>$p1,
        'PreviewConfig'=>$PreviewConfig
    ]) ?>

</div>
