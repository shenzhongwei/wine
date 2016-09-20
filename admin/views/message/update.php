<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\MessageList $model
 */

$this->title = 'Update Message List: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Message Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="message-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
