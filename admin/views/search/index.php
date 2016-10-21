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
                'header' => '热搜名',
                'attribute'=>'name',
                'class'=>EditableColumn::className(),
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'40%',
                'editableOptions'=>[
                    'asPopover' => false,
                    'formOptions'=>[
                        'action'=>Url::toRoute(['search/update']),
                    ],
                    'size'=>'sm',
                    'options' => ['class'=>'form-control', 'placeholder'=>'热搜名','maxlength'=>20]
                ],
            ],
            [
                'attribute'=>'order',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'class'=>EditableColumn::className(),
                'refreshGrid'=>true,
                'editableOptions'=>[
                    'asPopover' => false,
                    'formOptions'=>[
                        'action'=>Url::toRoute(['search/update']),
                    ],
                    'size'=>'sm',
                    'options' => ['class'=>'form-control', 'placeholder'=>'热搜排序','maxlength'=>1,'onkeyup'=>'this.value=this.value.replace(/\D/gi,"")']
                ],
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
        'toolbar'=> false,
        'responsive'=>false,
        'hover'=>true,
        'condensed'=>true,
        'panel' => [
            'type'=>'info',
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'before'=>$dataProvider->count >= 5 ? '':Html::a('<i class="fa fa-plus"> 发布热搜</i>', ['create'],['data-pjax'=>0,'type'=>'button', 'title'=>'发布热搜', 'class'=>'btn btn-primary']),
            'showPanel'=>true,
            'after'=>false,
            'footer'=>false,
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]); ?>

</div>
