<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\GoodSearch $searchModel
 */

$this->title = '商品列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-info-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a('Create Good Info', ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'merchant',
            'type',
            'name',
            'volum',
            'price',
            'unit',
            'pic',
            'number',
            'detail:ntext',
            'regist_at',
            'is_active',
            [
                'header' => '操作',
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
                            return Html::a('<i class="fa fa-arrow-up">上架</i>', $url, [
                                'title' => Yii::t('app', '上架'),
                                'class' => 'del btn btn-info btn-xs',
                            ]);
                        }else{
                            return Html::a('<i class="fa fa-arrow-down">下架</i>', $url, [
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
            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> 发布商品', ['create'], ['class' => 'btn btn-success']),
            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> 刷新列表', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>true
        ],
    ]); Pjax::end(); ?>

</div>
