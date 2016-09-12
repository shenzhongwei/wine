<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\EmployeeInfo $model
 */

$this->title = '新增配送人员';
$this->params['breadcrumbs'][] = ['label' => '配送人员列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-info-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
