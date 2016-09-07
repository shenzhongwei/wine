<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\OrderInfo $model
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-info-view">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>


    <?= DetailView::widget([
            'model' => $model,
            'condensed'=>false,
            'hover'=>true,
            'mode'=>Yii::$app->request->get('edit')=='t' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'panel'=>[
            'heading'=>$this->title,
            'type'=>DetailView::TYPE_INFO,
        ],
        'attributes' => [
            'id',
            'sid',
            'uid',
            'aid',
            'order_date',
            'order_code',
            'pay_id',
            'pay_date',
            'total',
            'discount',
            'ticket_id',
            'send_bill',
            'send_id',
            'send_code',
            'pay_bill',
            'state',
            'send_date',
            'is_del',
            'status',
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

</div>
