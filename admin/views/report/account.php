<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\jui\AutoComplete;
use admin\models\InoutPay;
use kartik\select2\Select2;
use common\helpers\ArrayHelper;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\ReportSearch $searchModel
 */
//var_dump($searchModel->sid);
//exit;
$this->title = '充值报表';
$this->params['breadcrumbs'][] = $this->title;
$pay = ['2'=>'支付宝','3'=>'微信'];
?>
<div class="account-list-index">

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'account_report',
            ],
            'neverTimeout'=>true,
        ],
        'columns' => [
            [
                'class'=>'kartik\grid\SerialColumn',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'header'=>'编号',
                'width'=>'10%'
            ],
            [
                'attribute'=>'pay_date',
                'label' => '充值时间',
                'hAlign'=>'center',
                'pageSummary'=>'合计',
                'width'=>'30%',
                'vAlign'=>'middle',
                'format'=>['date','php:Y-m-d H:i:s' ],
                'group'=>true,  // enable grouping
                'filterType'=>GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions'=>[
                    'presetDropdown'=>true,
                    'hideInput'=>true,
                    'language'=>'zh-CN',
                    'value'=>'',
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ],
                    ],
                ]
            ],
            [
                'attribute'=>'phone',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'20%',
                'header'=>'充值手机',
                'value' => function($model){
                    return empty($model->phone) ? '缺失':$model->phone;
                },
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions'=>[
                        'source'=>InoutPay::GetPhones()
                    ]
                ],
            ],
            [
                'header'=>'付款方式',
                'attribute'=>'pay_id',
                'width'=>'20%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    switch ($model->pay_id){
                        case 2:
                            return '支付宝';
                            break;
                        case 3:
                            return '微信';
                            break;
                        default:
                            return '未知';
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
            ],
            [
                'attribute'=>'sum',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'20%',
                'label'=>'付款金额',
                'pageSummary'=>true,
                'pageSummaryFunc'=>GridView::F_SUM,
                'footer'=>true
            ],
        ],
        'toolbar'=> [
            ['content'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['account'], ['class'=>'btn btn-default', 'title'=>'刷新报表'])],
            '{toggleData}',
            '{export}',
        ],
        'responsive'=>false,
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
            'footer'=>false,
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
