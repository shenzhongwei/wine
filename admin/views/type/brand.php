<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\grid\EditableColumn;
use yii\helpers\Url;
use admin\models\GoodBrand;
use kartik\editable\Editable;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\BrandSearch $searchModel
 */

$this->title = Yii::t('app', '品牌');
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
            [
                'label'=>'发布时间',
                'attribute'=>'regist_at',
                'hAlign'=>GridView::ALIGN_CENTER,
                'width'=>'240px',
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
                'width'=>'120px',
                'attribute' => 'is_active',
                'vAlign'=>GridView::ALIGN_LEFT,
                'trueLabel'=>'上架中',
                'falseLabel'=>'已下架',
                'trueIcon'=>'<label class="label label-info">上架中</label>',
                'falseIcon'=>'<label class="label label-danger">已下架</label>',
            ],
            [

                'header'=>'品牌logo',
                'width'=>'200px',
                'headerOptions'=>['class'=>'kartik-sheet-style'],
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'mergeHeader'=>true,
                'attribute'=>'logo',
                'class'=>EditableColumn::className(),
                'editableOptions'=>[
                    'format' => Editable::FORMAT_LINK,
                    'inputType' => Editable::INPUT_FILEINPUT,
                    'asPopover' => true,
                    'formOptions'=>[
                        'action'=>Url::toRoute(['type/update']),
                    ],
                    'size'=>'lg',
                    'pluginEvents' => [
                        "editableReset"=>"function(event) { $(this).parents('td').find('.form-group').children().val(''); }",
                    ],
                    'options' => [
                        'options'=>[
                            'name'=>'GoodType[url]',
                            'accept'=>'image/*',
                            'showUpload'=>false,
                            'showRemove'=>false,
                        ],
                        'pluginOptions'=>[
                            'showPreview' => false,
                            'initialPreview'=>false,
                            'uploadUrl' => Url::toRoute(['brand/upload']),
                            'maxFileSize'=>2000,
                            'previewFileType' => 'image',
//                            'initialPreviewAsData' => true,
                            'showUpload'=>true,
                            'showRemove'=>true,
                            'autoReplace'=>true,
                            'browseClass' => 'btn btn-success',
                            'uploadClass' => 'btn btn-info',
                            'removeClass' => 'btn btn-danger',
                            'maxFileCount'=>1,
                            'fileActionSettings' => [
                                'showZoom' => false,
                                'showUpload' => false,
                                'showRemove' => false,
                            ],
                        ],
                        'pluginEvents'=>[
                            'fileuploaderror'=>"function(){
                                                 $(this).parents('tr').find('.fileinput-upload-button').attr('disabled',true);
                                                }",
                            'fileclear'=>"function(){
                                    $(this).parents('.form-group').children().val('');
                                    }",
                            'fileuploaded'  => "function (object,data){
			                    $(this).parents('.form-group').children().val(data.response.imageUrl);
		                    }",
                            //错误的冗余机制
                            'error' => "function (){
			                    alert('data.error');
		                    }"
                        ]
                    ],
                ],
                "format" => "raw",
                'value'=>function($model){
                    return empty($model->logo) ? '<label class="label label-primary">暂无</label>':Html::img('../../../photo'.$model->logo,[
                        'width'=>"20px",'height'=>"20px"
                    ]);
                }
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
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['brand','id'=>$id], ['data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>'刷新列表'])
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
