<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\grid\EditableColumn;
use yii\helpers\Url;
use admin\models\GoodBrand;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\BrandSearch $searchModel
 */

$this->title = Yii::t('app', 'Good Brands');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-brand-index col-sm-8">

    <?php  echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'floatHeader'=>false,
        'pjax'=>true,
        'columns' => [
            [
                'class'=>'kartik\grid\SerialColumn',
                'contentOptions'=>['class'=>'kartik-sheet-style'],
                'width'=>'50px',
                'header'=>'',
                'headerOptions'=>['class'=>'kartik-sheet-style']
            ],
            [
                'header'=>'品牌名称',
                'attribute'=>'name',
                'width'=>'150px',
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
                'filterType'=>GridView::FILTER_SELECT2,
                'filterWidgetOptions'=>[
                    'data'=>GoodBrand::GetAllBrands($id),
                    'options'=>['placeholder'=>'请选择类型'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            'regist_at',
            'is_active',
            'logo',
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
                                'data-confirm' => '确认上架检索？',
                            ]);
                        }else{
                            return Html::a(Yii::t('app','Down'), $url, [
                                'title' => Yii::t('app', '下架该检索'),
                                'class' => 'btn btn-danger btn-xs',
                                'data-confirm' => '确认下架检索？',
                            ]);
                        }
                    }
                ],
            ],
        ],
        'responsive'=>false,
        'hover'=>true,
        'condensed'=>true,
        'bordered'=>true,
        'striped'=>false,
        'persistResize'=>false,
        'toolbar'=> [
            ['content'=>
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],['type'=>'button', 'title'=>'新增品牌', 'class'=>'btn btn-success']).
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['child','id'=>$model->id,'key'=>'goodBrands'], ['data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>'刷新列表'])
            ],
            '{toggleData}',
            '{export}',
        ],
        'panel' => [
            'heading'=>false,
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]); ?>

</div>
