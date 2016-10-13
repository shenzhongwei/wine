<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use yii\helpers\Url;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

$this->title = '轮播图列表';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile("@web/js/good/_script.js");
?>
<div class="good-pic-index">

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'goodpic',
            ],
            'neverTimeout'=>true,
        ],
        'columns' => [
            [
                'class'=>'kartik\grid\SerialColumn',
                'contentOptions'=>['class'=>'kartik-sheet-style'],
                'width'=>'10%',
                'header'=>'',
                'headerOptions'=>['class'=>'kartik-sheet-style']
            ],
            [
                'header' => '图片',
                'attribute'=>'pic',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'40%',
                "format" => "raw",
                'value'=>function($model){
                    return empty($model->pic) ? '<label class="label label-primary">暂无</label>':Html::img('../../../photo'.$model->pic,[
                        'width'=>"60px",'height'=>"40px","onclick"=>"ShowPic(this);",'style'=>'cursor:pointer','title'=>"点击放大"
                    ]);
                }
            ],
            [
                'header'=>'商品名称',
                'attribute'=>'gid',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'30%',
                "format" => "html",
                'value'=>function($model){
                    return Html::a($model->g->name.$model->g->volum,['good/view', 'id' => $model->gid],
                        ['title' => '查看商品详细','class'=>'btn btn-link btn-sm']
                    );
                }
            ],
            [
                'header' => '操作',
                'class' => 'kartik\grid\ActionColumn',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'20%',
                'buttons' => [
                    'view'=>function ($url, $model) {
                        return '';
                    },
                    'update' => function ($url, $model) {
                        return Html::a(Yii::t('app','Update'), '', [
                            'title' => Yii::t('app', '编辑'),
                            'class' => 'btn btn-primary btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a(Yii::t('app','Delete'), ['del-pic','id'=>$model->id,'key'=>$model->gid], [
                            'title' => Yii::t('app', '删除该轮播图'),
                            'class' => 'btn btn-danger btn-xs',
                            'data-confirm'=>'确认删除该轮播图？一旦删除无法恢复！',
                        ]);
                    }
                ],
            ],
        ],
        // set your toolbar
        'toolbar'=> false,
        'responsive'=>false,
        'hover'=>true,
        'condensed'=>true,
        'bordered'=>true,
        'striped'=>false,
        'floatHeader'=>false,
        'persistResize'=>false,
        'panel' => [
            'type'=>'info',
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'showPanel'=>true,
            'before'=>$dataProvider->count >= 4 ? '':Html::a('<i class="fa fa-plus"> 添加轮播图</i>', ['#'],['type'=>'button', 'title'=>'添加轮播图', 'class'=>'btn btn-primary']),
            'showFooter'=>true
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]); ?>

</div>

<?php

\yii\bootstrap\Modal::begin([
    'id' => 'pic-modal',
    'options' => [
        'tabindex' => false
    ],
]);
$updateUrl = \yii\helpers\Url::toRoute('pic-update');  //当前控制器下的view方法
$createUrl = \yii\helpers\Url::toRoute(['pic-create','type'=>$key]);  //当前控制器下的view方法
$Js = <<<JS
         $('.update').on('click', function () {  //查看详情的触发事件
          $('.good-price-create').remove();
          $('#price-modal').find('.modal-title').html('更新区间');
            $.get('{$updateUrl}', { id:$(this).closest('tr').data('key')  },
                function (data) {
                    $('#price-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
             );
         });
$('.create').on('click', function () {  //查看详情的触发事件
          $('.good-price-update').remove();
          $('#price-modal').find('.modal-title').html('新增区间');
            $.get('{$createUrl}', {},
                function (data) {
                    $('#price-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
             );
         });
JS;
$this->registerJs($Js);
\yii\bootstrap\Modal::end();
?>
<script>
    $("#price-modal").on("hidden.bs.modal", function(){
        $("#price-form")[0].reset();//重置表单
        $('#goodpricefield-end').val('+∞');
    });
    $("#update-modal").on("hidden.bs.modal", function(){
        $("#update-form")[0].reset();//重置表单
    });
</script>
