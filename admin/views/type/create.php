<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodType $model
 */

$this->title = Yii::t('app', '新增 {modelClass}', [
    'modelClass' => '类型',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '商品检索'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-type-create">
    <div class="panel panel-success">
        <div class="panel-heading">
            <?= '发布检索' ?>
        </div>
        <div class="panel-body">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
            </div>
        </div>

</div>
