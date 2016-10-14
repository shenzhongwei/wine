<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\MessageSearch $searchModel
 */

$this->title = '消息列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="message-list-index">
    <?php
    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'header'=>'序号',
                'class' => 'yii\grid\SerialColumn'
            ],
            [
                'attribute'=>'type_id',
                'value'=>function($data){
                    $query= \admin\models\Dics::find()->where(['type'=>'消息类型','id'=>$data->type_id])->one();
                    return empty($query)?'':$query->name;
                }
            ],
            'title',
            'content',
            [
                'attribute'=>'own_id',
                'value'=>function($data){
                    switch($data->type_id){
                        case 1:  //系统消息
                            $name='系统管理员';
                            break;
                        case 2: //用户消息

                            $query=\admin\models\UserInfo::findOne(['id'=>$data->own_id]);
                            $name=empty($query)?'':$query->phone.'('.$query->realname.')';

                            break;
                        case 3: //订单消息

                            $query=\admin\models\OrderInfo::findOne(['id'=>$data->own_id]);
                            $name=empty($query)?'':$query->order_code;
                            break;
                        case 4: //商品通知

                            $query=\admin\models\GoodInfo::findOne(['id'=>$data->own_id]);
                            $name=empty($query)?'':$query->name;
                            break;
                        default:
                            $name='无';
                            break;
                    }
                    return $name;
                }
            ],
            [
                'attribute'=>'target',
                'value'=>function($data){
                    $query=\admin\models\Dics::find()->where(['type'=>'消息跳转页面','id'=>$data->target])->one();
                    return empty($query)?'':$query->name;
                }
            ],
            'publish_at',
            [
                'header' => '操作',
                'class' => 'yii\grid\ActionColumn',
                'template'=> '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model) {
                            return Html::a('<i class="fa fa-close">删除</i>', $url, [
                                'class' => 'btn btn-success btn-xs',
                                'data'=>['confirm'=>'确认删除该消息？']
                            ]);
                    }
                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>false,

        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'before'=>$this->render('_search',['model'=>$searchModel]),
            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>刷新列表', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]);
    Pjax::end();
    ?>

</div>
