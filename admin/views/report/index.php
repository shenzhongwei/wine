<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\jui\AutoComplete;
use admin\models\ReportSearch;
use kartik\select2\Select2;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\ReportSearch $searchModel
 */
//var_dump($searchModel->sid);
//exit;
$this->title = '订单报表';
$this->params['breadcrumbs'][] = $this->title;
$pay = ['1'=>'余额','2'=>'支付宝','3'=>'微信'];

?>
<div class="report-list-index">

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterPosition' => GridView::FILTER_POS_HEADER,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'order_report',
            ],
            'neverTimeout'=>true,
        ],
        'columns' => [
            [
                'class'=>'kartik\grid\SerialColumn',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'1%',
                'header'=>'',
            ],
            [
                'attribute'=>'order_date',
                'label' => '时间',
                'width'=>'16%',
                'hAlign'=>'center',
                'pageSummary'=>'合计',
                'vAlign'=>'middle',
                'format'=>['date','php:Y-m-d H:i:s' ],
                'group'=>true,  // enable grouping
                'filterType'=>GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions'=>[
                    'name'=>'range',
                    'presetDropdown'=>true,
                    'hideInput'=>true
                ]
            ],
            [
                'attribute'=>'order_code',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'10%',
                'header'=>'订单号',
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions'=>[
                        'source'=>ReportSearch::getOrderCode(),
                    ]
                ],
                'group'=>true,  // enable grouping
            ],
            [
                'attribute'=>'good_type',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'header'=>'类型',
                'width'=>'8%',
                'value' => function($model){
                    return (empty($model->g)||empty($model->g->type0)) ? '缺失':$model->g->type0->name;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>ReportSearch::getTypes(),
                'filterWidgetOptions'=>[
                    'maintainOrder' => true,
                    'toggleAllSettings' => [
//                        'selectLabel' => '<i class="glyphicon glyphicon-ok-circle"></i> 全选',
                        'selectOptions' => ['class' => 'text-success'],
                        'unselectOptions' => ['class' => 'text-danger'],
                    ],
                    'options'=>['placeholder'=>'请选择类型','multiple' => false],
                    'pluginOptions' => ['allowClear' => true,
                        'tags' => false,
//                        'maximumInputLength' => 5
                    ],
                ],
            ],
            [
                'attribute'=>'gid',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'header'=>'产品名称',
                'width'=>'10%',
                'value' => function($model){
                    return empty($model->g) ? '缺失':$model->g->name.$model->g->volum;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>ReportSearch::getGoods(),
                'filterWidgetOptions'=>[
                    'maintainOrder' => true,
                    'toggleAllSettings' => [
                        'selectOptions' => ['class' => 'text-success'],
                        'unselectOptions' => ['class' => 'text-danger'],
                    ],
                    'options'=>['placeholder'=>'请选择产品','multiple' => false],
                    'pluginOptions' => ['allowClear' => true,
                    ],
                ],
            ],
            [
                'attribute'=>'amount',
                'label' => '数量',
                'width'=>'4%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    return $model->amount.$model->g->unit;
                }
            ],

            [
                'header'=>'下单酒庄',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'10%',
                'attribute'=>'sid',
                'value'=>function($model){
                    return empty($model->s) ? '缺失':$model->s->name;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>ReportSearch::getShops(),
                'filterWidgetOptions'=>[
//                    'data'=>
                    'maintainOrder' => true,
                    'toggleAllSettings' => [
//                        'selectLabel' => '<i class="glyphicon glyphicon-ok-circle"></i> 全选',
                        'selectOptions' => ['class' => 'text-success'],
                        'unselectOptions' => ['class' => 'text-danger'],
                    ],
                    'options'=>['placeholder'=>'请选择下单酒庄','multiple' => false],
                    'pluginOptions' => ['allowClear' => true,
                        'tags' => false,
//                        'maximumInputLength' => 5
                    ],
                    ],
                'group'=>true,  // enable grouping
                'subGroupOf'=>1 // supplier column index is the parent group
            ],
            [
                'header'=>'付款方式',
                'attribute'=>'pay_id',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    switch ($model->pay_id){
                        case 2:
                            return '支付宝';
                            break;
                        case 3:
                            return '财付通';
                            break;
                        default:
                            return '余额';
                    }
                },
                'filterType'=>Select2::className(),
                'filterWidgetOptions'=>[
                    'data'=>$pay,
                    'hideSearch'=>true,
                    'maintainOrder' => true,
                    'toggleAllSettings' => [
                        'selectOptions' => ['class' => 'text-success'],
                        'unselectOptions' => ['class' => 'text-danger'],
                    ],
                    'options'=>['placeholder'=>'支付方式'],
                    'pluginOptions'=>[
                        'allowClear'=>true,
                    ]
                ],
                'group'=>true,  // enable grouping
                'subGroupOf'=>1 // supplier column index is the parent group
            ],

            [
                'label'=>'原价',
                'attribute'=>'cost',
                'width'=>'7%',
                'format'=>['decimal', 2],
                'hAlign'=>'center',
                'vAlign'=>'middle',
//                'value'=>function($model){
//                    return $model->cost.'元';
//                },
                'group'=>true,  // enable grouping
                'subGroupOf'=>1 ,// supplier column index is the parent group
                'pageSummary'=>true,
                'pageSummaryFunc'=>GridView::F_SUM,
                'footer'=>true
            ],
            [
                'label'=>'成交价',
                'attribute'=>'pay_bill',
                'width'=>'7%',
                'format'=>['decimal', 2],
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    return ($model->pay_bill-$model->o->send_bill);
                },
                'group'=>true,  // enable grouping
                'subGroupOf'=>1, // supplier column index is the parent group
                'pageSummary'=>true,
                'pageSummaryFunc'=>GridView::F_SUM,
                'footer'=>true
            ],
            [
                'label'=>'利润',
                'attribute'=>'profit',
                'width'=>'7%',
                'format'=>['decimal', 2],
                'hAlign'=>'center',
                'vAlign'=>'middle',
//                'value'=>function($model){
//                    return ($model->profit).'元';
//                },
                'pageSummary'=>true,
                'pageSummaryFunc'=>GridView::F_SUM,
                'footer'=>true,
                'group'=>true,  // enable grouping
                'subGroupOf'=>1 // supplier column index is the parent group
            ],
        ],
        'toolbar'=> [
            ['content'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['class'=>'btn btn-default', 'title'=>'刷新报表'])],
            '{toggleData}',
            '{export}',
        ],
        'responsive'=>false,
        'hover'=>false,
        'condensed'=>true,
        'bordered'=>true,
        'striped'=>false,
        'floatHeader'=>false,
        'showPageSummary'=>true,
        'persistResize'=>false,
        'panel' => [
            'type'=>'info',
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'showPanel'=>true,
            'showFooter'=>false,
            'after'=>false,
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]);
    ?>

</div>
<script language="JavaScript">
    $(function (){
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
    });
</script>
