<?php
use kartik\grid\GridView;
use kartik\helpers\Html;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 */
?>
<div class="user-login-index" style="padding: 0%">

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'=>'{items}',
        'tableOptions'=>[
            'style'=>'margin-bottom:0px'
        ],
        'columns' => [
            [
                'header' => '登录名',
                'attribute'=>'username',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'25%',
            ],
            [
                'header' => '最近登录',
                'attribute'=>'last_login_time',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'25%',
            ],
            [
                'header' => '状态',
                'attribute'=>'status',
                'class'=>'kartik\grid\BooleanColumn',
                'width'=>'25%',
                'trueIcon'=>'<span class="label label-primary"><i class="fa fa-check"></i> 激活中</span>',
                'falseIcon'=>'<span class="label label-default"><i class="fa fa-times"></i> 已冻结</span>',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],
            [
                'header'=>'操作',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'25%',
                'class' => 'kartik\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-send"> 发送推送</i>', '#', [
                            'class' => 'del btn btn-success btn-xs',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return '';
                    },
                    'delete' => function ($url, $model) {
                        return '';
                    }
                ],
            ],
        ],
        'bordered'=>false,
        'panel' => false,
        'responsive'=>false,
        'condensed'=>true,
    ]); ?>

</div>