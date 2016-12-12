<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use admin\models\UserInfoSearch;
use admin\models\UserInfo;
use yii\jui\AutoComplete;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\UserInfoSearch $searchModel
 */

$this->title = '用户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-info-index">

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'userinfo',
            ],
            'neverTimeout'=>true,
        ],
        'columns' => [
            [
                'header'=>'',
                'width'=>'5%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'contentOptions'=>['class'=>'kartik-sheet-style'],
                'class' => 'kartik\grid\SerialColumn'
            ],
            [
                'attribute'=>'nickname',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'header'=>'昵称',
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions' => [
                        'source' =>UserInfoSearch::geAllName(),
                    ],
                ]
            ],
            [
                'attribute'=>'phone',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'header'=>'<i class="fa fa-phone"></i> 手机号',
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions' => [
                        'source' =>UserInfoSearch::geAllPhone(),
                    ],
                ]

            ],
            [
                'label'=>'账户余额',
                'width'=>'10%',
                'attribute'=>'end',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'format'=>['decimal', 2],
                'value'=>function($model){
                    return $model->end;
                },
                'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
            ],
            [
                'header'=>'邀请人',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=>'invite_user',
                'value'=>function($model){
                    if(empty($model->invite_user_id)){
                        return '未填写';
                    }else{
                        $user=UserInfo::findOne(['id'=>$model->invite_user_id]);
                        if(empty($user)){
                            return '丢失';
                        }else{
                            return $user->nickname;
                        }
                    }
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>UserInfoSearch::geInviter(),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'邀请人'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            //邀请人不需要
//            [
//                'attribute'=>'invite_code',
//                'width'=>'10%',
//                'hAlign'=>'center',
//                'vAlign'=>'middle',
//                'header'=>'邀请码',
//                'filterType'=>AutoComplete::className(),
//                'filterWidgetOptions'=>[
//                    'clientOptions' => [
//                        'source' =>UserInfoSearch::getAllCode(),
//                    ],
//                ]
//            ],
            [
                'attribute'=>'created_time',
                'width'=>'20%',
                'label'=>'注册时间',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    return $model->created_time;
                },
                'filterType'=>GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions'=>[
                    'language'=>'zh-CN',
                    'value'=>'',
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ],
                        'opens'=>'left'
                    ],
                ]
            ],
            [
                'label'=>'会员状态',
                'width'=>'10%',
                'attribute'=>'is_vip',
                'trueLabel'=>'会员',
                'falseLabel'=>'非会员',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'class'=>'kartik\grid\BooleanColumn',
                'trueIcon'=>'<span class="glyphicon glyphicon-ok" aria-hidden="true" style="color: #5c9ccc"></span>',
                'falseIcon'=>'<span class="glyphicon glyphicon-remove" aria-hidden="true" style="color: #5c9ccc"></span>',
            ],
            [
                'attribute'=>'status',
                'class'=>'kartik\grid\BooleanColumn',
                'width'=>'10%',
                'trueLabel'=>'激活中',
                'falseLabel'=>'非激活',
                'trueIcon'=>'<label class="label label-info">激活中</label>',
                'falseIcon'=>'<label class="label label-danger">已冻结</label>',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],
            [
                'header'=>'操作',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'15%',
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{view}&nbsp;&nbsp;{delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a(Yii::t('app', 'View'), $url, [
                            'title' => '查看用户详情',
                            'class' => 'del btn btn-primary btn-xs',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return '';
                    },
                    'delete' => function ($url, $model) {
                        if($model->status == 1){
                            return Html::a(Yii::t('app', 'Lock'), $url, [
                                'title' => Yii::t('app', 'lock'),
                                'class' => 'del btn btn-warning btn-xs',
                                'data'=>['confirm'=>Yii::t('app', 'UserLock')]

                            ]);
                        }elseif ($model->status == 0){
                            return Html::a(Yii::t('app', 'Unlock'), $url, [
                                'title' => Yii::t('app', 'unlock'),
                                'class' => 'del btn btn-info btn-xs',
                                'data'=>['confirm'=>Yii::t('app', 'UserUnlock')]

                            ]);
                        }
                    }
                ],
            ],
        ],
        // set your toolbar
        'toolbar'=> [
            ['content'=>
//                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],['data-pjax'=>0,'type'=>'button', 'title'=>'发布商品', 'class'=>'btn btn-primary']).
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], [ 'class'=>'btn btn-default', 'title'=>'刷新列表'])
            ],
            '{toggleData}',
            '{export}',
        ],
        'responsive'=>false,
        'condensed'=>true,
        'panel' => [
            'type'=>'info',
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'after'=>false,
            'showPanel'=>true,
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]); ?>

</div>
<script language="JavaScript">
    $(function (){
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
        $('.ui-autocomplete').css('z-index','99999');
        $('.datepicker-days').css('z-index','99999');
    });
</script>
