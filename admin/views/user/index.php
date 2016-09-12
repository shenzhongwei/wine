<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

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
    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'header'=>'序号',
                'class' => 'yii\grid\SerialColumn'
            ],
            'phone',
            'sex',
            [
                'attribute'=>'head_url',
                'format'=>'html',
                'value'=>function($data){
                    if(!empty($data->head_url)){
                        $img='<img src="../../../photo'.$data->head_url.'" width="50" height="50">';
                    }else{
                        $img='<img src="../../../photo/logo/user_default.jpg" width="50" height="50">';
                    }
                    return $img;
                }
            ],
            'birth',
            'nickname',
            'realname',
            [
                'attribute'=>'invite_user_id',
                'value'=>function($data){
                    $user=\admin\models\UserInfo::findOne(['id'=>$data->invite_user_id]);
                    return empty($user)?'':$user->realname.'('.$user->nickname.')';
                }
            ],
            'invite_code',
            [
                'attribute'=>'is_vip',
                'options' => ['width' => '100px;'],
                'value'=>function($data){
                    return empty($data->is_vip)?'否':'是';
                }
            ],
            [
                'attribute'=>'status',
                'value'=>function($data){
                    return empty($data->status)?'删除':'正常';
                }
            ],

            [
                'attribute'=>'created_time',
                'value'=>function($data){
                    return $data->created_time;
                }
            ],
            [
                'header'=>'操作',
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-eye">查看</i>', $url, [
                            'title' => Yii::t('app', '查看'),
                            'class' => 'del btn btn-primary btn-xs',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fa fa-edit">编辑</i>', $url, [
                            'title' => Yii::t('app', '编辑'),
                            'class' => 'del btn btn-success btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        if($model->status == 1){
                            return Html::a('<i class="fa fa-remove">删除</i>', $url, [
                                'title' => Yii::t('app', '删除订单'),
                                'class' => 'del btn btn-danger btn-xs',
                                'data'=>['confirm'=>'你确定要删除该条记录吗？']

                            ]);
                        }
                    }
                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,

        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'before'=>$this->render('_search', ['model' => $searchModel]),
            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>刷新列表', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>true
        ],
    ]); Pjax::end(); ?>

</div>
