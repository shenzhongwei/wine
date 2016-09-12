<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\GoodVipSearch $searchModel
 */

$this->title = '会员商品表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-vip-index">
    <p>
        <?php /* echo Html::a('Create Good Vip', ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(['id'=>'goodinfos','timeout'=>3000]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],

            [
                'header'=>'商品名称',
                'attribute'=>'gid',
                'format'=>'html',
                'value'=>function($model) {
                    return Html::a($model->g->name.$model->g->volum,['good/view', 'id' => $model->id],
                        ['title' => '查看商品详细','class'=>'btn btn-link btn-sm']
                    );
                }
            ],

            [
                'header'=>'原价',
                'value'=>function($model) {
                    return '¥'.$model->g->price.'/'.$model->g->unit;
                }
            ],
            [
                'label'=>'会员价',
                'attribute'=>'price',
                'value'=>function($model) {
                    return '¥'.$model->price.'/'.$model->g->unit;
                }
            ],
            [
                'header'=>'商品状态',
                'format' => 'raw',
                'value' => function ($model) {
                    $state =  $model->g->is_active==0 ? '<label class="label label-danger">已下架</label>':'<label class="label label-info">上架中</label>';
                    return $state;
                },
            ],
//            'limit',
            [
                'label'=>'会员商品状态',
                'attribute' => 'is_active',
                'format' => 'raw',
                'value' => function ($model) {
                    $state =  $model->is_active==0 ? '<label class="label label-danger">已下架</label>':'<label class="label label-info">上架中</label>';
                    return $state;
                },
            ],

            [
                'header' => '操作',
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-eye">查看</i>', $url, [
                            'title' => Yii::t('app', Yii::t('app','View')),
                            'class' => 'btn btn-info btn-xs',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a(Yii::t('app','Update'), $url, [
                            'title' => Yii::t('app', '编辑'),
                            'class' => 'btn btn-primary btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        if($model->is_active == 0){
                            return Html::a(Yii::t('app','Up'), $url, [
                                'title' => Yii::t('app', '上架该商品'),
                                'class' => 'btn btn-success btn-xs',
                            ]);
                        }else{
                            return Html::a(Yii::t('app','Down'), $url, [
                                'title' => Yii::t('app', '下架该商品'),
                                'class' => 'btn btn-danger btn-xs',
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
            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> 刷新列表', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>true
        ],
    ]); Pjax::end(); ?>

</div>
