<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use yii\widgets\Pjax;
use kartik\grid\GridView;
/**
 * @var yii\web\View $this
 * @var \admin\models\UserAccount $model
 * @var yii\data\ActiveDataProvider $dataProvider
 */

$this->title = '账户明细';
$this->params['breadcrumbs'][] = ['label' => '账户明细', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<!--账户明细-->

<div class="account-inout-index">
    <?php
    echo GridView::widget([
        "options" => [
            // ...其他设置项
            "id" => "account_inout"
        ],
        'dataProvider' => $dataProvider,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'account_inout_pjax',
            ],
            'neverTimeout'=>true,
        ],
        'columns' => [
            [
                'class'=>'kartik\grid\SerialColumn',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'3%',
                'header'=>'',
            ],
            [
                'attribute'=>'aio_date',
                'label' => '时间',
                'width'=>'15%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'format'=>['date','php:Y-m-d H:i:s' ],
            ],
            [
                'header'=>'收支类型',
                'attribute'=>'type',
                'width'=>'12%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'format' => 'html',
                'value'=> function($model){
                    $typeArr = [1=>'订单支付', 2=>'订单收入', 3=>'活动奖励', 4=>'余额充值'];
                    return empty($typeArr[$model->type]) ? '<span class="no-set">未设置</span>':$typeArr[$model->type];
                }
            ],
            [
                'label'=>'金额',
                'hAlign'=>'center',
                'width'=>'8%',
                'vAlign'=>'middle',
                'attribute'=>'sum',
                'value'=> function($model){
                    return '¥'.$model->sum;
                },
            ],
            [
                'attribute'=>'discount',
                'label' => '赠送金额',
                'width'=>'8%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=> function($model){
                    return '¥'.$model->discount;
                },
            ],
            [
                'attribute'=>'note',
                'header' => '备注',
                'width'=>'64%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],
        ],
        // set your toolbar
        'toolbar'=> [
            ['content'=>
                Html::a('<i class="glyphicon glyphicon-repeat">刷新列表</i>', ['view','id'=>$model->id], [ 'class'=>'btn btn-default', 'title'=>'刷新列表'])
            ],
            '{toggleData}',
            '{export}',
        ],
        'responsive'=>false,
        'condensed'=>true,
        'panel' => [
            'type'=>'info',
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'before'=>'',
            'after'=>false,
            'showPanel'=>true,
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]); ?>

</div>
<script language="JavaScript">
    $(function () {
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
        $('.ui-autocomplete').css('z-index','99999');
        $('.datepicker-days').css('z-index','99999');
    })
</script>