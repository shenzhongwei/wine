<?php
use kartik\grid\GridView;
use kartik\helpers\Html;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 */

?>
<div class="shop-order-index" style="padding: 0%">

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'=>'{items}{pager}',
        'pager'=>[
            'options' => [
                'class' => 'pagination'
            ]
        ],
        'pjax'=>true,
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'shoporder',
            ],
            'neverTimeout'=>true,
        ],
        'tableOptions'=>[
            'style'=>'margin-bottom:0px'
        ],
        'columns' => [
            [
                'header' => '下单时间',
                'attribute'=>'order_date',
                'format'=>["date", "php:Y年m月d日"],
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'10%',
            ],
            [
                'header' => '订单编号',
                'attribute'=>'order_code',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'10%',
                'format' => 'html',
                'value'=> function($model){
                    return Html::a($model->order_code,['order/view', 'id' => $model->id],
                        ['title' => '查看订单详细','class'=>' btn-link btn-sm']
                    );
                },
            ],
            [
                'header' => '总金额',
                'attribute'=>'total',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],
            [
                'header' => '优惠金额',
                'attribute'=>'discount',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],
            [
                'header' => '使用积分',
                'attribute'=>'point',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],
            [
                'header' => '下单用户',
                'attribute'=>'uid',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    return $model->u->phone;
                }
            ],
            [
                'header' => '付款金额',
                'attribute'=>'pay_bill',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],
            [
                'header' => '付款方式',
                'attribute'=>'pay_bill',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    $pay = [
                        '1'=>'余额',
                        '2'=>'支付宝',
                        '3'=>'微信',
                    ];
                    return $pay[$model->pay_id];
                }
            ],
            [
                'header' => '进度',
                'attribute'=>'state',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'format'=>'html',
                'value'=>function($model){
                    switch ($model->state){
                        case 1:
                            return '<span class="label label-default">新订单</span>';
                        case 2:
                            return '<span class="label label-primary">已付款</span>';
                        case 3:
                            return '<span class="label label-info">已装箱</span>';
                        case 4:
                            return '<span class="label label-info">配送中</span>';
                        case 5:
                            return '<span class="label label-info">已送达</span>';
                        case 6:
                            return '<span class="label label-success">已收货</span>';
                        case 7:
                            return '<span class="label label-success">已评价</span>';
                        case 99:
                            return '<span class="label label-warning">已退款</span>';
                        case 100:
                            return '<span class="label label-warning">已取消</span>';
                    }
                }
            ],
            [
                'header' => '状态',
                'attribute'=>'is_del',
                'class'=>'kartik\grid\BooleanColumn',
                'width'=>'10%',
                'trueIcon'=>'<span class="label label-primary"><i class="fa fa-check"></i> 已删</span>',
                'falseIcon'=>'<span class="label label-default"><i class="fa fa-times"></i> 未删</span>',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],
        ],
        'bordered'=>false,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> 门店订单 </h3>',
            'before'=>false,
            'after'=>false,
            'showPanel'=>false,
            'showFooter'=>false
        ],
        'responsive'=>false,
        'condensed'=>true,
    ]); ?>

</div>