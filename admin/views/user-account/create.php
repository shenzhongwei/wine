<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\UserAccount $model
 */

$this->title = 'Create User Account';
$this->params['breadcrumbs'][] = ['label' => 'User Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-account-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
