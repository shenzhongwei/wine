<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use admin\models\GoodType;
use kartik\grid\EditableColumn;
use kartik\editable\Editable;
use yii\helpers\Url;
use kartik\widgets\FileInput;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\TypeSearch $searchModel
 */

$this->title = Yii::t('app', 'Good Types');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-type-index">

    <?php
//    Pjax::begin(['id'=>'typeinfo','timeout'=>5000]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax'=>true,
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'typeinfo'
            ],
            'neverTimeout'=>true,
        ],
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'columns' => [
            [
                'class'=>'kartik\grid\SerialColumn',
                'contentOptions'=>['class'=>'kartik-sheet-style'],
                'width'=>'4%',
                'header'=>'',
                'headerOptions'=>['class'=>'kartik-sheet-style']
            ],
            [
                'header'=>'类型名称',
                'attribute'=>'name',
                'class'=>EditableColumn::className(),
                'editableOptions'=>[
                    'asPopover' => true,
                    'formOptions'=>[
                        'action'=>Url::toRoute(['type/update']),
                    ],
                    'size'=>'sm',
                    'options' => ['class'=>'form-control', 'placeholder'=>'输入名称']
                ],
                'width'=>'19%',
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
                'width'=>'25%',
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
                'width'=>'14%',
                'attribute' => 'is_active',
                'vAlign'=>GridView::ALIGN_LEFT,
                'trueLabel'=>'上架中',
                'falseLabel'=>'已下架',
                'trueIcon'=>'<label class="label label-info">上架中</label>',
                'falseIcon'=>'<label class="label label-danger">已下架</label>',
            ],
            [

                'header'=>'图标',
                'width'=>'12%',
                'headerOptions'=>['class'=>'kartik-sheet-style'],
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'mergeHeader'=>true,
                'attribute'=>'logo',
                'refreshGrid' => true,
                'class'=>EditableColumn::className(),
                'editableOptions'=>[
                    'format' => Editable::FORMAT_LINK,
                    'inputType' => Editable::INPUT_FILEINPUT,
                    'asPopover' => true,
                    'formOptions'=>[
                        'action'=>Url::toRoute(['type/update']),
                    ],
                    'size'=>'md',
                    'placement'=>'bottom',
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
                            'uploadUrl' => Url::toRoute(['type/upload']),
                            'maxFileSize'=>200,
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
                'header'=>'子检索',
                'class' =>  'kartik\grid\ActionColumn',
                'width'=>'16%',
                'headerOptions'=>['class'=>'kartik-sheet-style'],
                'buttons'=>[
                    'view' => function ($url, $model) {
                        return Html::a('点击查看', ['type/view', 'id' => $model->id,'key'=>'brand'], [//指定弹框的id
                            'class' => 'btn-link btn-xs detail',
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
                'width'=>'10%',
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
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],['data-pjax'=>0,'type'=>'button', 'title'=>'发布大类', 'class'=>'btn btn-primary']).
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['class'=>'btn btn-default', 'title'=>'刷新列表'])
            ],
            '{toggleData}',
            '{export}',
        ],

        'responsive'=>false,
        'condensed'=>true,
        'panel' => [
            'type'=>'info',
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'after'=>false,
            'showPanel'=>true,
            'footer'=>false,
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]);
//    Pjax::end();
    ?>

</div>
