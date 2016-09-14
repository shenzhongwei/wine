<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodInfo $model
 */

$this->title = '新增商品';
$this->params['breadcrumbs'][] = ['label' => 'Good Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-info-create ibox-content">
    <div class="row">
        <h2><?= Html::encode($this->title) ?></h2>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
