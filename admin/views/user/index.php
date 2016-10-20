<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use admin\models\UserAccount;
use admin\models\UserInfo;
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
                'class' => 'kartik\grid\SerialColumn'
            ],
            [
                'attribute'=>'nickname',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'header'=>'昵称',
            ],
            [
                'attribute'=>'phone',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'header'=>'<i class="fa fa-phone"></i> 手机号',
            ],
            [
                'label'=>'账户余额',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'format'=>'html',
                'value'=>function($model){
                    $useraccount=UserAccount::find()->where(['target'=>$model->id,'level'=>2,'type'=>1])->one();
                    return '<strong style="color: #f1a417">'.(empty($useraccount)?'0.00':$useraccount->end).'</strong>';
                }
            ],
            [
                'attribute'=>'realname',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'header'=>'姓名',
            ],
            [
                'header'=>'邀请人',
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
                            return $user->phone;
                        }
                    }
                }
            ],
            [
                'attribute'=>'invite_code',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'header'=>'邀请码',
            ],
            [
                'label'=>'会员状态',
                'attribute'=>'is_vip',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'class'=>'kartik\grid\BooleanColumn',
                'trueIcon'=>'<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>',
                'falseIcon'=>'<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>',
            ],
            [
                'attribute'=>'status',
                'class'=>'kartik\grid\BooleanColumn',
                'trueIcon'=>'<label class="label label-info">激活中</label>',
                'falseIcon'=>'<label class="label label-danger">已冻结</label>',
                'hAlign'=>'center',
                'vAlign'=>'middle',
            ],

            [
                'attribute'=>'created_time',
                'label'=>'注册时间',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($model){
                    return $model->created_time;
                }
            ],
            [
                'header'=>'操作',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{view}&nbsp;&nbsp;{delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-eye">查看</i>', $url, [
                            'title' => Yii::t('app', 'view'),
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
        'hover'=>true,
        'condensed'=>true,
        'bordered'=>true,
        'striped'=>false,
        'floatHeader'=>false,
        'persistResize'=>false,
        'panel' => [
            'type'=>'info',
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'after'=>false,
            'showPanel'=>true,
            'showFooter'=>true
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
    });
</script>
