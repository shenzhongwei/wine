<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\MessageList $model
 */

$this->title = '发布消息';
$this->params['breadcrumbs'][] = ['label' => 'Message Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-list-create" style="margin: 10px auto">
    <?= $this->render('_form', [
        'model' => $model,
        'wa_type'=>$wa_type
    ]) ?>

</div>
