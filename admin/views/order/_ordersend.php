<?php
/**
 * Created by PhpStorm.
 * User: 沈中伟
 * Date: 2016/12/1
 * Time: 23:34
 */

use kartik\grid\GridView;
use kartik\helpers\Html;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 */
$statusArr = [
    '<span class="label label-warning">删除</span>',
    '<span class="label label-success">正常</span>',
    '<span class="label label-info">配送中</span>',
    '<span class="label label-danger">下岗</span>'
];
?>
<div class="order-send-index" style="padding: 0%">

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'=>'{items}',
        'tableOptions'=>[
            'style'=>'margin-bottom:0px'
        ],
        'columns' => [
            [
                'header' => '配送人',
                'attribute'=>'send_id',
                'width'=>'14%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    return $model->send->name;
                }
            ],
            [
                'header' => '配送人手机',
                'attribute'=>'send_id',
                'width'=>'19%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    return $model->send->phone;
                }
            ],
            [
                'header' => '配送人类型',
                'attribute'=>'send_id',
                'width'=>'19%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    return $model->send->type == 1 ? '店铺配送员':'商家配送员';
                }
            ],
            [
                'header' => '物流编号',
                'attribute'=>'send_code',
                'width'=>'19%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],
            [
                'header' => '送达时间',
                'attribute'=>'send_date',
                'format'=>'raw',
                'width'=>'19%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    return $model->state<5 ? '<span class="not-set">未送达</span>':
                        (empty($model->send_date) ? '<span class="not-set">用户先收货</span>':date('m月d日 H时i分'));
                }
            ],
            [
                'header' => '状态',
                'attribute'=>'send_id',
                'format'=>'raw',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    $statusArr = [
                        '<span class="label label-warning">删除</span>',
                        '<span class="label label-success">正常</span>',
                        '<span class="label label-info">配送中</span>',
                        '<span class="label label-danger">下岗</span>'
                    ];
                    return $statusArr[$model->send->status];
                }
            ],
        ],
        'panel' => false,
        'responsive'=>false,
        'condensed'=>true,
    ]); ?>

</div>