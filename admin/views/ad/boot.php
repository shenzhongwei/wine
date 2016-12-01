<?php

use kartik\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\AdListSearch $searchModel
 */

$this->title = '启动页';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile("@web/js/good/_script.js");
$count = $dataProvider->totalCount;
?>

<div class="ad-list-index">
    <?php
    echo GridView::widget([
        "options" => [
            // ...其他设置项
            "id" => "boot_pic"
        ],
        'dataProvider' => $dataProvider,
        'pjax'=>true,
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'boot-pic'
            ],
            'neverTimeout'=>true,
        ],
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'columns' => [
            [
                'attribute'=>'pic',
                'header'=>'图片',
                'width'=>'30%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                "format" => "raw",
                'value'=>function($model){
                    return Html::img('../../../../photo'.$model->pic,[
                        'width'=>"100px",'height'=>"180px","onclick"=>"ShowBoot(this);",'style'=>'cursor:pointer','title'=>"点击放大"
                    ]);
                }
            ],
            [
                'attribute'=>'pic_url',
                'header'=>'图片链接',
                'width'=>'40%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'format' => 'raw',
                'value'=> function($model){
                    return Html::a(str_replace('http://','',$model->pic_url),'#',
                        ['class'=>'btn-link btn-sm']
                    );
                },
            ],
            [
                'header' => '操作',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'30%',
                'class' =>  'kartik\grid\ActionColumn',
                'buttons' => [
                    'view'=>function(){
                        return '';
                    },
                    'update' => function ($url, $model) {
                        return Html::a(Yii::t('app','Update'), $url, [
                            'data-pjax'=>0,
                            'data-toggle' => 'modal',    //弹框
                            'data-target'=>'#boot-modal',
                            'title' => Yii::t('app', '编辑'),
                            'class' => 'btn btn-primary btn-xs update',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a(Yii::t('app','Delete'), ['delete','name'=>'boot','id'=>$model->id], [
                            'title' => Yii::t('app', '删除此启动页'),
                            'class' => 'btn btn-danger btn-xs',
                            'data-confirm' => "确认删除该启动图？一旦删除若不上传新启动图打开APP将无法显示启动图",
                        ]);
                    }
                ],
            ],
        ],
        'toolbar'=> [
            ['content'=>
                $count>=1 ? '':Html::a('<i class="glyphicon glyphicon-plus">添加启动页</i>', ['#'],[
                    'id'=>'boot-add',
                    'data-pjax'=>0,
                    'type'=>'button',
                    'title'=>'添加启动页',
                    'class'=>'btn btn-primary add',
                    'data-toggle' => 'modal',    //弹框
                    'data-target' => '#boot-modal',    //指定弹框的id
                ])
            ],
        ],
        'responsive'=>false,
        'condensed'=>true,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'after'=>false,
            'showFooter'=>false,
            'footer'=>false,
        ],
    ]);
    ?>

</div>
<?php
\yii\bootstrap\Modal::begin([
    'id' => 'boot-modal',
    'options' => [
        'tabindex' => false
    ],
    'header' => '<h4 class="modal-title"></h4>',
]);
\yii\bootstrap\Modal::end();
?>
<script language="JavaScript">
    $(function(){
        $(document).ready(init());
        $(document).on('pjax:complete', function() {init();});
    });
    function init() {
        $('.btn-link').on('click',function () {
            url = $(this).text();
            window.open('http://'+url);
        });
        $('.ui-autocomplete').css('z-index','99999');
        $('.datepicker-days').css('z-index','99999');
        $('.add').on('click', function () {  //查看详情的触发事件
            $('.ad-list-form').remove();
            $('#boot-modal').find('.modal-title').html('新增启动图');
            $.get(toRoute('ad/create'), { key:'boot'  },
                function (data) {
                    $('#boot-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
            );
        });
        $('.update').on('click', function () {  //查看详情的触发事件
            $('.ad-list-form').remove();
            $('#boot-modal').find('.modal-title').html('编辑启动图');
            $.get(toRoute('ad/update'), { id:$(this).closest('tr').data('key'),key:'boot'  },
                function (data) {
                    $('#boot-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
            );
        });
        $("#boot-modal").on("hidden.bs.modal", function () {
            $("#ad-form")[0].reset();//重置表单
        });
    }
</script>
