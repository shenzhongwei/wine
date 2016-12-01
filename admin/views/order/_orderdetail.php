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
<div class="oorder-detail-index" style="padding: 0%">

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'=>'{items}',
        'tableOptions'=>[
            'style'=>'margin-bottom:0px'
        ],
        'columns' => [
            [
                'header' => '商品',
                'attribute'=>'gid',
                'width'=>'35%',
                'format'=>'html',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    return empty($model->g) ? '<span class="not-set">商品信息异常</span>':$model->g->name.$model->g->volum;
                }
            ],
            [
                'header' => '数量',
                'attribute'=>'amount',
                'width'=>'13%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],
            [
                'header' => '单价',
                'attribute'=>'single_price',
                'width'=>'13%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],
            [
                'header' => '原价',
                'width'=>'13%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    return empty($model->g) ? '<span class="not-set">商品信息异常</span>':$model->g->cost;
                }
            ],
            [
                'header' => '市场价',
                'width'=>'13%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    return empty($model->g) ? '<span class="not-set">商品信息异常</span>':$model->g->pro_price;
                }
            ],
            [
                'header' => '总价',
                'attribute'=>'total_price',
                'width'=>'13%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],
        ],
        'panel' => false,
        'responsive'=>false,
        'condensed'=>true,
    ]); ?>

</div>