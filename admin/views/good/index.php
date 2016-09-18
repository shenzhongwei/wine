<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\GoodSearch $searchModel
 */

$this->title = '商品列表';
$this->params['breadcrumbs'][] = $this->title;
\admin\assets\AppAsset::register($this);
// here
$this->registerJsFile("@web/js/good/_script.js");
?>
<div class="good-info-index">
    <?php Pjax::begin(['id'=>'goodinfos','timeout'=>3000]);
        echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'header'=>'序号',
                'class' => 'kartik\grid\SerialColumn'
            ],
            [
                'header'=>'商品名称',
                'attribute'=>'name',
                'format' => 'raw',
                'value'=> function($model){
                    return Html::a($model->name.$model->volum,['good/view', 'id' => $model->id],
                        ['title' => '查看商品详细','style'=>'color:#2a62bc;font-size:15px']
                    );
                }
            ],
            [
                'header'=>'归属商户',
                'attribute'=>'merchant',
                'format' => 'raw',
                'value'=> function($model){
                    return Html::a($model->merchant0->name,['merchant/view', 'id' => $model->merchant0->id],
                        ['title' => '查看商户信息','style'=>'color:#2a62bc;font-size:15px']
                    );
                },
                'headerOptions'=>['width'=>120]

            ],
            [
                'header'=>'类型',
                'attribute'=>'type',
                'value'=> function($model){
                    return empty($model->type0) ? '无':$model->type0->name;
                }
            ],
            [
                'header'=>'品牌',
                'attribute'=>'brand',
                'value'=> function($model){
                    return $model->brand0->name;
                }
            ],
            [
                'attribute'=>'price',
                'label' => '单价',
                'value'=> function($model){
                    return '¥'.$model->price.'/'.$model->unit;
                }
            ],
            [

                'header'=>'图片',
                'attribute'=>'pic',
                "format" => "raw",
                'value'=>function($model){
                    return Html::img('../../../photo'.$model->pic,[
                        'width'=>"50px",'height'=>"50px","onclick"=>"ShowImg(this);",'style'=>'cursor:pointer','title'=>"点击放大"
                    ]);
                }
            ],
            [
                'header'=>'编号',
                'attribute'=>'number',
            ],
            [
                'header'=>'详情',
                'attribute'=>'detail',
                'format'=>'html',
                'value'=>function($model){
                    return '<a class="btn btn-link btn-xs" href="'.Url::toRoute(['good/detail','id'=>$model->id]).'">点击查看</a>';
                }
            ],
            [
                'label'=>'发布时间',
                'attribute'=>'regist_at',
                'format'=>['date','php:Y年m月d日'],
                'value'=>function($model){
                    return $model->regist_at;
                }
            ],
            [
                'label'=>'状态',
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
                                'data-confirm' => Yii::t('app', 'GoodUpSure'),
                            ]);
                        }else{
                            return Html::a(Yii::t('app','Down'), $url, [
                                'title' => Yii::t('app', '下架该商品'),
                                'class' => 'btn btn-danger btn-xs',
                                'data-confirm' => Yii::t('app', 'GoodDownSure'),
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
            'showFooter'=>true,
        ],
    ]); Pjax::end(); ?>

</div>
<script language="JavaScript">
    $(function (){
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
    });
</script>


