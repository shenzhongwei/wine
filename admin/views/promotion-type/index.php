<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\PromotionTypeSearch $searchModel
 */

$this->title = '优惠券分类';
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
                    switch($data->class){
                        case 1: $str='<span style="color: #00a2d4">有券</span>'; break;
                        case 2: $str='<span style="color: #47a447">无券</span>'; break;
                        default: $str='<span style="color: red">类别错误</span>'; break;
                    }
                    return $str;
                }
            ],
            [
                'attribute'=>'group',
                'format'=>'html',
                'value'=>function($data){
                    switch($data->group){
                        case 1: $str='<span style="color: #00a2d4">优惠</span>'; break;
                        case 2: $str='<span style="color: #47a447">特权</span>'; break;
                        case 3: $str='<span style="color: #f4bb51">赠送</span>'; break;
                        default: $str='<span style="color: red">组别错误</span>'; break;
                    }
                    return $str;
                }
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
                'value'=>function($data){
                    return $data->is_active==0?'否':'是';
                },
            ],
            [
                'header'=>'操作',
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update}&nbsp;&nbsp;&nbsp;{delete}',
                'buttons' => [
//                    'view' => function ($url, $model) {
//                        return Html::a('<i class="fa fa-eye">查看</i>', $url, [
//                            'title' => Yii::t('app', '查看'),
//                            'class' => 'del btn btn-primary btn-xs',
//                        ]);
//                    },
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fa fa-edit">编辑</i>', $url, [
                            'title' => Yii::t('app', '编辑'),
                            'class' => 'del btn btn-success btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        if($model->is_active == 0){
                            return Html::a('<i class="fa fa-arrow-circle-down">上架</i>', $url, [
                                'title' => Yii::t('app', '上架'),
                                'class' => 'del btn btn-info btn-xs',
                            ]);
                        }else{
                            return Html::a('<i class="fa fa-arrow-circle-o-up">下架</i>', $url, [
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