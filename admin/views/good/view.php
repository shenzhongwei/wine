<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodInfo $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Good Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-info-view">
    <div class="page-header">
        <h2><?= Html::encode($this->title) ?></h2>
    </div>


    <?= DetailView::widget([
            'model' => $model,
            'condensed'=>true,
            'hover'=>true,
            'mode'=>Yii::$app->request->get('edit')=='t' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'panel'=>[
            'heading'=>$this->title,
            'type'=>DetailView::TYPE_INFO,
        ],
        'attributes' => [
            'id',
            'merchant',
            'type',
            'brand',
            'smell',
            'color',
            'dry',
            'boot',
            'breed',
            'country',
            'style',
            'name',
            'volum',
            'price',
            'unit',
            'pic',
            'number',
            'detail:ntext',
            'order',
            'regist_at',
            'is_active',
            'active_at',
        ],
        'enableEditMode'=>true,
        'deleteOptions'=>[
            'url'=>'delete?id='.$model->id,
        ]
    ]) ?>

</div>
