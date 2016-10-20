<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\GoodVipSearch $searchModel
 */

$this->title = '会员商品表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-vip-index">
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
//        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
//        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'goodvip',
            ],
            'neverTimeout'=>true,
        ],
        'columns' => [
            [
                'header'=>'',
                'width'=>'10%',
                'class' => 'kartik\grid\SerialColumn',
                'hAlign'=>'center',
                'vAlign'=>'middle',
//                'contentOptions'=>['class'=>'kartik-sheet-style'],
//                'headerOptions'=>['class'=>'kartik-sheet-style']
            ],

            [
                'header'=>'商品名称',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=>'gid',
                'format'=>'html',
                'width'=>'15%',
                'value'=>function($model) {
                    return Html::a($model->g->name.$model->g->volum,['good/view', 'id' => $model->id],
                        ['title' => '查看商品详细','class'=>'btn btn-link btn-sm']
                    );
                }
            ],

            [
                'label'=>'原价',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=>'g.price',
                'width'=>'15%',
                'value'=>function($model) {
                    return '¥'.$model->g->price.'/'.$model->g->unit;
                }
            ],
            [
                'label'=>'会员价',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=>'price',
                'width'=>'15%',
                'value'=>function($model) {
                    return '¥'.$model->price.'/'.$model->g->unit;
                }
            ],
            [
                'header'=>'商品状态',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'15%',
                'format' => 'raw',
                'value' => function ($model) {
                    $state =  $model->g->is_active==0 ? '<label class="label label-danger">已下架</label>':'<label class="label label-info">上架中</label>';
                    return $state;
                },
            ],
            [
                'label'=>'会员商品状态',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'15%',
                'attribute' => 'is_active',
                'format' => 'raw',
                'value' => function ($model) {
                    $state =  $model->is_active==0 ? '<label class="label label-danger">已下架</label>':'<label class="label label-info">上架中</label>';
                    return $state;
                },
            ],

            [
                'header' => '操作',
                'class' => 'kartik\grid\ActionColumn',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'15%',
                'buttons' => [
                    'view'=>function ($url, $model) {
                        return '';
                    },
                    'update' => function ($url, $model) {
                        return Html::a(Yii::t('app','Update'), $url, [
                            'title' => Yii::t('app', '编辑'),
                            'class' => 'btn btn-primary btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        if($model->is_active == 0){
                            return Html::a(Yii::t('app','Up'), $url, [
                                'title' => Yii::t('app', '上架该商品'),
                                'class' => 'btn btn-success btn-xs',
                                'data-confirm'=>'确定上架该会员商品？'
                            ]);
                        }else{
                            return Html::a(Yii::t('app','Down'), $url, [
                                'title' => Yii::t('app', '下架该商品'),
                                'class' => 'btn btn-danger btn-xs',
                                'data-confirm'=>'确定下架该会员商品？'
                            ]);
                        }
                    }
                ],
            ],
        ],
        'toolbar'=> [
            ['content'=>
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],['data-pjax'=>0,'type'=>'button', 'title'=>'发布会员产品', 'class'=>'btn btn-primary']).
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['class'=>'btn btn-default', 'title'=>'刷新列表'])
            ],
            '{toggleData}',
            '{export}',
        ],

        'responsive'=>false,
        'hover'=>true,
        'condensed'=>true,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'after'=>false,
            'before'=>$this->render('_search', ['model' => $searchModel]),
            'showFooter'=>true
        ],
    ]);  ?>

</div>

<script language="JavaScript">
    $(function (){
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
    });
</script>