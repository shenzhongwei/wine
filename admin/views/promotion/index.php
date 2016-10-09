<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use admin\models\PromotionType;
use admin\models\PromotionInfo;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\PromotionInfoSearch $searchModel
 */

$this->title = '活动促销列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promotion-info-index">

    <?php
    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'header'=>'序号',
                'class' => 'kartik\grid\SerialColumn'
            ],
            [
                'attribute'=>'pt_id',
                'value'=>function($data){
                    return PromotionType::getPromotionTypeName($data->pt_id);
                }
            ],
            [
                'attribute'=>'limit',
                'value'=>function($data){
                    return PromotionType::getPromotionRangeById($data->limit);
                }
            ],
            [
                'attribute'=>'target_id',
                'value'=>function($data){
                    return PromotionInfo::getNameByRange($data);
                }
            ],
            'name',
            'condition',
            'discount',
            [
                'attribute'=> 'valid_circle',
                'value'=>function($data){
                    return empty($data->valid_circle)?'永久有效':$data->valid_circle.'天';
                }
            ],

            [
                'attribute'=>'start_at',
                'value'=>function($data){
                    return empty($data->start_at)?'':date('Y-m-d H:i:s',$data->start_at);
                }
            ],
            [
                'attribute'=>'end_at',
                'value'=>function($data){
                    return empty($data->end_at)?'':date('Y-m-d H:i:s',$data->end_at);
                }
            ],
            [
                'attribute'=> 'time',
                'value'=>function($data){
                    return empty($data->time)?'无数次':$data->time.'次';
                }
            ],

            [
                'attribute'=>'regist_at',
                'value'=>function($data){
                    return empty($data->regist_at)?'':date('Y-m-d H:i:s',$data->regist_at);
                }
            ],
            [
                'attribute'=>'is_active',
                'format'=>'html',
                'value'=>function($data){
                    return empty($data->is_active)?'<p><span class="label label-danger"><i class="fa fa-times"></i>否</span></p>':
                        '<p><span class="label label-primary"><i class="fa fa-check"></i>是</span></p>';
                },
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
                        if($model->is_active == 0){
                            return Html::a('<i class="fa fa-arrow-circle-o-up">上架</i>', $url, [
                                'title' => Yii::t('app', '上架'),
                                'class' => 'del btn btn-info btn-xs',
                            ]);
                        }else{
                            return Html::a('<i class="fa fa-arrow-circle-o-down">下架</i>', $url, [
                                'title' => Yii::t('app', '下架'),
                                'class' => 'del btn btn-danger btn-xs',
                            ]);
                        }
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
            'before'=>$this->render('_search', ['model' => $searchModel]),
            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> 刷新列表', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>true
        ],
    ]); Pjax::end(); ?>

</div>
