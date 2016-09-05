<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodInfo $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '信息列表', 'url' => ['index']];
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
            'url'=>\yii\helpers\Url::toRoute(['good/delete','id'=>$model->id],true),
            'label'=>$model->is_active ? '<i class="glyphicon glyphicon-arrow-down"></i>':'<i class="glyphicon glyphicon-arrow-up"></i>',
            'title' => Yii::t('app', $model->is_active ? '下架':'上架'),
            'confirm'=>$model->is_active ? '一旦下架，用户将看不到该产品信息，确认下架?':'上架后该产品变为显示状态，确认上架?',
        ]
    ]) ?>

</div>
