<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\UserAccount $model
 */

$this->title = 'Update User Account: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-account-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
