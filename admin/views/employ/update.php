<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\EmployeeInfo $model
 */

$this->title = '更新配送人员信息' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Employee Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="employee-info-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
