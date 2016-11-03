<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodType $model
 */

$this->title = ' 发布大类';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '商品检索'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-type-create">
    <div class="panel panel-success">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-plus"></span><?= $this->title ?>
        </div>
        <div class="panel-body">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
            </div>
        </div>

</div>
