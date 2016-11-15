<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\OrderSend $model
 */

$this->title = '订单发配';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_sendform', [
    'model' => $model,
]) ?>