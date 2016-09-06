<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\MerchantInfo $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Merchant Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="merchant-info-view">

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
            'name',
            'wa_id',
            [
                'attribute'=>'region',
                'format'=>'html',
                'value'=>$model->region."&nbsp;&nbsp;&nbsp;
                        (&nbsp;<span style='color: #ff3333'>".$model->province."</span>
                        <span style='color: #ff3333'>".$model->city."</span>
                        <span style='color: #ff3333'>".$model->district."</span>&nbsp;)",
            ],

            'address',
            'lat',
            'lng',
            'phone',
            'registe_at',
            [
                'attribute'=>'is_active',
                'value'=>$model->is_active==0?'否':'是',

            ],
            [
                'attribute'=>'active_at',
                'value'=>empty($model->active_at)?'':$model->active_at,

            ],


        ],
        'deleteOptions'=>[
            'url'=>['delete', 'id' => $model->id],
            'data'=>[
                'confirm'=>Yii::t('app', 'Are you sure you want to delete this item?'),
                'method'=>'post',
            ],
        ],
        'enableEditMode'=>true,
    ]) ?>
    <p style="margin: 0 auto;text-align: center;">
        <?= Html::a('返回', 'index', ['class' => 'btn btn-primary']) ?>
    </p>
</div>
