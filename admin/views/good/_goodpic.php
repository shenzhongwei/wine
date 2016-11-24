<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use yii\helpers\Url;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var integer $key
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
                    return empty($model->pic) ? '<label class="label label-primary">暂无</label>':Html::img('../../../../photo'.$model->pic,[
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
                            'data-toggle' => 'modal',    //弹框
                            'data-target'=>'#pic-modal',
                            'title' => Yii::t('app', '编辑'),
                            'class' => 'update btn btn-primary btn-xs',
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
        'responsive'=>false,
        'condensed'=>true,
        'panel' => [
            'type'=>GridView::TYPE_SUCCESS,
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'showPanel'=>true,
            'before'=>$dataProvider->count >= 4 ? false:Html::a('<i class="fa fa-plus"> 添加轮播图</i>', ['#'],[
                'type'=>'button', 'title'=>'添加轮播图', 'class'=>'create btn btn-primary',
                'data-target'=>'#pic-modal','data-toggle' => 'modal',    //弹框
            ]),
            'after'=>false,
            'footer'=>false,
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]); ?>

</div>

<?php

\yii\bootstrap\Modal::begin([
    'id' => 'pic-modal',
    'size'=>'modal-lg',
    'options' => [
        'tabindex' => false
    ],
    'header' => '<h4 class="modal-title"></h4>',
]);
$updateUrl = \yii\helpers\Url::toRoute(['pic-update','key'=>$key]);  //
$createUrl = \yii\helpers\Url::toRoute(['pic-create','key'=>$key]);  //
$Js = <<<JS
         $('.update').on('click', function () {  //查看详情的触发事件
          $('.good-pic-form').remove();
          $('#pic-modal').find('.modal-title').html('编辑轮播图');
            $.get('{$updateUrl}', { id:$(this).closest('tr').data('key')  },
                function (data) {
                    $('#pic-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
             );
         });
$('.create').on('click', function () {  //查看详情的触发事件
          $('.good-pic-form').remove();
          $('#pic-modal').find('.modal-title').html('新增轮播图');
            $.get('{$createUrl}', {},
                function (data) {
                    $('#pic-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
             );
         });
JS;
$this->registerJs($Js);
\yii\bootstrap\Modal::end();
?>
<script>
    $("#pic-modal").on("hidden.bs.modal", function(){
        $("#pic-form")[0].reset();//重置表单
    });
</script>
