<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use admin\models\GoodType;
use kartik\grid\EditableColumn;
use kartik\editable\Editable;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\TypeSearch $searchModel
 */

$this->title = Yii::t('app', 'Good Types');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-type-index col-sm-8">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Good Type',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

<!--    --><?php //Pjax::begin(['id'=>'typeinfos','timeout'=>5000]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'columns' => [
            [
                'class'=>'kartik\grid\SerialColumn',
                'contentOptions'=>['class'=>'kartik-sheet-style'],
                'width'=>'1%',
                'header'=>'',
                'headerOptions'=>['class'=>'kartik-sheet-style']
            ],
            [
                'header'=>'类型名称',
                'attribute'=>'name',
                'refreshGrid'=>true,
                'class'=>EditableColumn::className(),
                'editableOptions'=>[
                    'asPopover' => true,
                    'formOptions'=>[
                        'action'=>Url::toRoute(['type/update']),
                    ],
                    'size'=>'sm',
                    'options' => ['class'=>'form-control', 'placeholder'=>'输入名称']
                ],
                'width'=>'6%',
                'filterType'=>GridView::FILTER_SELECT2,
                'filterWidgetOptions'=>[
                    'data'=>GoodType::GetAllTypes(),
                    'options'=>['placeholder'=>'请选择类型'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'label'=>'发布时间',
                'attribute'=>'regist_at',
                'hAlign'=>GridView::ALIGN_CENTER,
                'width'=>'6%',
                'format'=>['date','php:Y年m月d日'],
                'value'=>function($model){
                    return $model->regist_at;
                },
                'filterType'=>GridView::FILTER_DATE,
                'filterWidgetOptions'=>[
                    // inline too, not bad
                    'language' => 'zh-CN',
                    'options' => ['placeholder' => '选择发布日期','readonly'=>true],
                    'pluginOptions' => [
                        'format' => 'yyyy年mm月dd日',
                        'autoclose' => true,
                    ]
                ]
            ],
            [
                'label'=>'状态',
                'class'=>'kartik\grid\BooleanColumn',
                'width'=>'5%',
                'attribute' => 'is_active',
                'vAlign'=>GridView::ALIGN_LEFT,
                'trueLabel'=>'上架中',
                'falseLabel'=>'已下架',
                'trueIcon'=>'<label class="label label-info">上架中</label>',
                'falseIcon'=>'<label class="label label-danger">已下架</label>',
            ],
            [

                'header'=>'图标',
                'width'=>'8%',
                'headerOptions'=>['class'=>'kartik-sheet-style'],
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'mergeHeader'=>true,
                'attribute'=>'pic',
                "format" => "raw",
                'value'=>function($model){
                    return empty($model->pic) ? '<label class="label label-primary">暂无</label>':Html::img('../../../photo'.$model->pic,[
                        'width'=>"50px",'height'=>"50px","onclick"=>"ShowImg(this);",'style'=>'cursor:pointer','title'=>"点击放大"
                    ]);
                }
            ],
            [
                'header'=>'子检索',
                'class' =>  'kartik\grid\ActionColumn',
                'width'=>'5%',
                'headerOptions'=>['class'=>'kartik-sheet-style'],
                'buttons'=>[
                    'view' => function ($url, $model) {
                        return Html::a('点击查看', '#', [
                            'id'=>'detail',//属性
                            'data-toggle' => 'modal',    //弹框
                            'data-target' => '#type-modal',    //指定弹框的id
                            'class' => 'detail btn btn-link btn-xs',
                            'data-id' => $model->id,
                        ]);
                    },
                    'update' =>  function ($url, $model) {
                        return '';
                    },
                    'delete' =>function ($url, $model) {
                        return '';
                    },
                ],
            ],

            [
                'header' => '操作',
                'width'=>'5%',
                'class' =>  'kartik\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return '';
                    },
                    'update' => function ($url, $model) {
                        return '';
                    },
                    'delete' => function ($url, $model) {
                        if($model->is_active == 0){
                            return Html::a(Yii::t('app','Up'), $url, [
                                'title' => Yii::t('app', '上架该检索'),
                                'class' => 'btn btn-success btn-xs',
                                'data-confirm' => Yii::t('app', 'TypeUpSure'),
                            ]);
                        }else{
                            return Html::a(Yii::t('app','Down'), $url, [
                                'title' => Yii::t('app', '下架该检索'),
                                'class' => 'btn btn-danger btn-xs',
                                'data-confirm' => Yii::t('app', 'TypeDownSure'),
                            ]);
                        }
                    }
                ],
            ],
        ],
        // set your toolbar
        'toolbar'=> [
            ['content'=>
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],['type'=>'button', 'title'=>'发布商品', 'class'=>'btn btn-primary']).
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
            'type'=>'info',
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'showPanel'=>true,
            'showFooter'=>true
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]);
//    Pjax::end(); ?>

</div>
