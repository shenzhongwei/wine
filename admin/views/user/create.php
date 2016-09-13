<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var admin\models\UserInfo $model
 */

$this->title = '新增用户';
$this->params['breadcrumbs'][] = ['label' => 'User Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-info-create">

    <?= $this->render('_form', [
        'model' => $model,
        'p1'=>$p1,
        'PreviewConfig'=>$PreviewConfig
    ]) ?>

</div>
