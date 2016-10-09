
<?php

use yii\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\grid\EditableColumn;
use admin\models\GoodBrand;
use kartik\editable\Editable;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\BrandSearch $searchModel
 * @var admin\models\GoodType $model
 */
$brandModel = new GoodBrand(['scenario'=>'create']);
$brandModel->type = $model->id;

?>
<?php echo
GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterUrl' => Url::toRoute(['view', 'id' => $model->id, 'key' => 'brand']),
    'filterPosition' => GridView::FILTER_POS_HEADER,
    'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
    'headerRowOptions'=>['class'=>'kartik-sheet-style'],
    'filterRowOptions'=>['class'=>'kartik-sheet-style'],
    'floatHeader'=>false,
//        'pjax'=>true,
    'columns' => [
        [
            'class'=>'kartik\grid\SerialColumn',
            'contentOptions'=>['class'=>'kartik-sheet-style'],
            'width'=>'3%',
            'header'=>'',
            'headerOptions'=>['class'=>'kartik-sheet-style']
        ],
        [
            'header'=>'品牌名称',
            'attribute'=>'name',
            'width'=>'12%',
//                'refreshGrid'=>true,
            'class'=>EditableColumn::className(),
            'editableOptions'=>[
                'asPopover' => true,
                'formOptions'=>[
                    'action'=>Url::toRoute(['type/update-brand','type'=>$model->id]),
                ],
                'size'=>'sm',
                'options' => ['class'=>'form-control', 'placeholder'=>'输入名称']
            ],
            'filterType'=>GridView::FILTER_SELECT2,
            'filterWidgetOptions'=>[
                'data'=>GoodBrand::GetAllBrands($model->id),
                'options'=>['placeholder'=>'请选择品牌'],
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
            'width'=>'12%',
            'attribute' => 'is_active',
            'vAlign'=>GridView::ALIGN_LEFT,
            'trueLabel'=>'上架中',
            'falseLabel'=>'已下架',
            'trueIcon'=>'<label class="label label-info">上架中</label>',
            'falseIcon'=>'<label class="label label-danger">已下架</label>',
        ],
        [
            'header'=>'类型',
            'attribute'=>'type',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'mergeHeader'=>true,
            'width'=>'8%',
            'refreshGrid'=>true,
            'class'=>EditableColumn::className(),
            'editableOptions'=>[
                'format' => Editable::FORMAT_LINK,
                'inputType' => Editable::INPUT_SELECT2,
                'asPopover' => true,
                'placement'=>'bottom',
                'formOptions'=>[
                    'action'=>Url::toRoute(['type/update-brand','type'=>$model->id]),
                ],
                'size'=>'md',
                'options' => [
                    'class'=>'form-control',
                    'data'=>GoodBrand::GetAllTypes(),
                    'options'=>['placeholder'=>'请选择类型'],
                    'pluginOptions' => ['allowClear' => true],
                ]
            ],
            'value'=>function($model){
                return $model->type0->name;
            }
        ],
        [

            'header'=>'品牌logo',
            'width'=>'10%',
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
                'placement'=>'bottom',
                'formOptions'=>[
                    'action'=>Url::toRoute(['type/update-brand','type'=>$model->id]),
                ],
                'header'=>'品牌logo（宽高：50*100）',
                'size'=>'md',
                'pluginEvents' => [
                    "editableReset"=>"function(event) { $(this).parents('td').find('.form-group').children().val(''); }",
                ],
                'options' => [
                    'options'=>[
                        'name'=>'GoodBrand[url]',
                        'accept'=>'image/*',
                        'showUpload'=>false,
                        'showRemove'=>false,
                    ],
                    'pluginOptions'=>[
                        'showPreview' => false,
                        'initialPreview'=>false,
                        'uploadUrl' => Url::toRoute(['type/brand-upload']),
                        'maxFileSize'=>1000,
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
                    'width'=>"70px",'height'=>"30px"
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
                        return Html::a(Yii::t('app', 'Up'), ['child-delete', 'key' => 'brand', 'id' => $model->id], [
                            'title' => Yii::t('app', '上架该检索'),
                            'class' => 'btn btn-success btn-xs',
                            'data-confirm' => '确认上架该检索？',
                        ]);
                    }else{
                        return Html::a(Yii::t('app', 'Down'), ['child-delete', 'key' => 'brand', 'id' => $model->id], [
                            'title' => Yii::t('app', '下架该检索'),
                            'class' => 'btn btn-danger btn-xs',
                            'data-confirm' => '确认下架该检索？',
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
            Html::a('<i class="glyphicon glyphicon-plus"></i>', '',[
                'data-toggle' => 'modal',    //弹框
                'data-target' => '#brand-modal',    //指定弹框的id
                'type'=>'button', 'title'=>'新增品牌', 'class'=>'btn btn-success'
            ]).
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['view','id'=>$model->id,'key'=>'brand'], ['data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>'刷新列表'])
        ],
        '{toggleData}',
        '{export}',
    ],
    'panel' => [
        'heading'=>false,
//            'before'=>$this->render('_brandsearch', ['model' => $searchModel,'id'=>$model->id]),
    ],
    'export'=>[
        'fontAwesome'=>true
    ],
])
?>
<?php

\yii\bootstrap\Modal::begin([
    'id' => 'brand-modal',
    'options' => [
        'tabindex' => false
    ],
    'header' => '<h4 class="modal-title">新增品牌</h4>',
]);
echo $this->render('_brandcreate',['model'=>$brandModel]);
\yii\bootstrap\Modal::end();
?>
<script>
    $("#brand-modal").on("hidden.bs.modal", function(){
        $("#brand-form")[0].reset();//重置表单
    });
</script>