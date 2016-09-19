<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use admin\models\PromotionType;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\PromotionTypeSearch $searchModel
 */

$this->title = '活动分类';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promotion-type-index">

    <?php Pjax::begin();
       echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'header'=>'序号',
                'class' => 'yii\grid\SerialColumn'
            ],
            [
                'attribute'=>'class',
                'format'=>'html',
                'value'=>function($data){
                    return PromotionType::getPromotionTypes($data);
                },
            ],
            [
                'attribute'=>'group',
                'format'=>'html',
                'value'=>function($data){
                    return PromotionType::getPromotionGroup($data);
                },
            ],
            'name',
            [
                'attribute'=>'regist_at',
                'value'=>function($data){
                    return date('Y-m-d H:i:s',$data->regist_at);
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
                'template'=>'{update}&nbsp;&nbsp;&nbsp;{delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fa fa-edit ">编辑</i>', $url, [
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
           'floatHeader'=>true,

           'panel' => [
               'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
               'type'=>'info',
               'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i>新增分类', ['create'], ['class' => 'btn btn-success']),
               'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>刷新列表', ['index'], ['class' => 'btn btn-info']),
               'showFooter'=>true
           ],
    ]);
    Pjax::end(); ?>

</div>
