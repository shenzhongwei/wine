<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use yii\helpers\Url;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

$this->title = '热搜列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hoot-search-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'hotsearch',
            ],
            'neverTimeout'=>true,
        ],
        'columns' => [
            [
                'class'=>'kartik\grid\SerialColumn',
                'contentOptions'=>['class'=>'kartik-sheet-style'],
                'width'=>'10%',
                'header'=>'',
                'headerOptions'=>['class'=>'kartik-sheet-style']
            ],
            [
                'header' => '图片',
                'attribute'=>'pic',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'40%',
            ],
            [
                'attribute'=>'gid',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'30%',
            ],
            [
                'header' => '操作',
                'class' => 'kartik\grid\ActionColumn',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'20%',
                'buttons' => [
                    'view'=>function ($url, $model) {
                        return '';
                    },
                    'update' => function ($url, $model) {
                        return '';
                    },
                    'delete' => function ($url, $model) {
                        return Html::a(Yii::t('app','Delete'), $url, [
                            'title' => Yii::t('app', '删除该热搜'),
                            'class' => 'btn btn-danger btn-xs',
                            'data-confirm'=>'确认删除该热搜？一旦删除无法恢复！',
                        ]);
                    }
                ],
            ],
        ],
        // set your toolbar
        'toolbar'=> [
            ['content'=>
                $dataProvider->count >= 5 ? '':Html::a('<i class="fa fa-plus"> 发布热搜</i>', ['create'],['data-pjax'=>0,'type'=>'button', 'title'=>'发布热搜', 'class'=>'btn btn-primary'])
            ],
            '{toggleData}',
            '{export}',
        ],
        'responsive'=>false,
        'hover'=>true,
        'condensed'=>true,
        'bordered'=>true,
        'striped'=>false,
        'floatHeader'=>false,
        'persistResize'=>false,
        'panel' => [
            'type'=>'info',
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'showPanel'=>true,
            'showFooter'=>true
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]); ?>

</div>