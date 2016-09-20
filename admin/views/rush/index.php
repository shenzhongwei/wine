<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\RushSearch $searchModel
 */

$this->title = '抢购管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-rush-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a('Create Good Rush', ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true, // pjax is set to always true for this demo
        'columns' => [
            [
                'class'=>'kartik\grid\SerialColumn',
                'contentOptions'=>['class'=>'kartik-sheet-style'],
                'width'=>'36px',
                'header'=>'',
                'headerOptions'=>['class'=>'kartik-sheet-style']
            ],
            [
                'attribute'=>'gid',
                'header'=>'商品名称',
                'vAlign'=>'middle',
                'width'=>'210px',
                'format'=>'html',
                'value'=>function($model) {
                    return Html::a($model->g->name.$model->g->volum,['good/view', 'id' => $model->id],
                        ['title' => '查看商品详细','class'=>'btn btn-link btn-sm']
                    );
                }
            ],
            'gid',
            'price',
            'limit',
            'amount',
            ['attribute'=>'start_at','format'=>['time',(isset(Yii::$app->modules['datecontrol']['displaySettings']['time'])) ? Yii::$app->modules['datecontrol']['displaySettings']['time'] : 'H:i:s A']],
            ['attribute'=>'end_at','format'=>['time',(isset(Yii::$app->modules['datecontrol']['displaySettings']['time'])) ? Yii::$app->modules['datecontrol']['displaySettings']['time'] : 'H:i:s A']],
            [
                'attribute'=>'is_active',
                'label'=>'抢购状态',
                'format' => 'raw',
                'value' => function ($model) {
                    $state =  $model->g->is_active==0 ? '<label class="label label-danger">已下架</label>':'<label class="label label-info">上架中</label>';
                    return $state;
                },
            ],
            [
                'label'=>'抢购状态',
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'is_active',
                'vAlign'=>'middle'
            ],

            [
                'header' => '操作',
                'class' => 'kartik\grid\ActionColumn',
                'buttons' => [
                    'view'=>function ($url, $model) {
                        return '';
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
                                'data-confirm'=>'确定上架该抢购商品？',
                            ]);
                        }else{
                            return Html::a(Yii::t('app','Down'), $url, [
                                'title' => Yii::t('app', '下架该商品'),
                                'class' => 'btn btn-danger btn-xs',
                                'data-confirm'=>'确定下架该抢购商品？',
                            ]);
                        }
                    }
                ],
            ],
        ],
        // set your toolbar
        'toolbar'=> [
            ['content'=>
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],['type'=>'button', 'title'=>'发布抢购', 'class'=>'btn btn-primary']).
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>'刷新列表'])
            ],
            '{toggleData}',
            '{export}',
        ],
        'responsive'=>false,
        'hover'=>true,
        'condensed'=>true,
        'bordered'=>true,
        'striped'=>false,
        'floatHeader'=>true,
        'persistResize'=>false,
        'panel' => [
            'type'=>GridView::TYPE_SUCCESS,
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'showPanel'=>true,
            'showFooter'=>true
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]); Pjax::end(); ?>

</div>