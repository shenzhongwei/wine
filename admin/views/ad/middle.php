<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use admin\models\AdList;
use admin\models\Dics;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\AdListSearch $searchModel
 */

$this->title = '首页中间广告';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile("@web/js/good/_script.js");
$count = $dataProvider->totalCount;
?>

<div class="ad-list-index">
    <?php
    echo GridView::widget([
        "options" => [
            // ...其他设置项
            "id" => "head_pic"
        ],
        'dataProvider' => $dataProvider,
        'pjax'=>true,
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'head-pic'
            ],
            'neverTimeout'=>true,
        ],
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'columns' => [
            [
                'header'=>'广告类型',
                'attribute'=>'type',
                'width'=>'15%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($data){
                    $query=Dics::find()->where(['type'=>'图片类型','id'=>$data->type])->one();
                    return empty($query)?'':$query->name;
                },
            ],
            [
                'header'=>'类型对象',
                'width'=>'15%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=> 'target_id',
                'value'=>function($data){
                    return AdList::getOneName($data);
                },
            ],
            [
                'header'=>'图片',
                'attribute'=>'pic',
                'width'=>'30%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                "format" => "raw",
                'value'=>function($data){
                    return Html::img('../../../photo'.$data->pic,[
                        'width'=>"180px",'height'=>"100px","onclick"=>"ShowAd(this);",'style'=>'cursor:pointer','title'=>"点击放大"
                    ]);
                }
            ],
            [
                'attribute'=>'pic_url',
                'header'=>'图片链接',
                'width'=>'20%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'format' => 'raw',
                'value'=> function($model){
                    return $model->type==1 ? Html::a($model->pic_url,$model->pic_url,
                        ['target' => '_blank','class'=>'btn btn-link btn-sm']
                    ):'<span class="not-set">(非外链广告无需设置)</span>';
                },
            ],
            [
                'header' => '操作',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'20%',
                'class' =>  'kartik\grid\ActionColumn',
                'buttons' => [
                    'view'=>function(){
                        return '';
                    },
                    'update' => function ($url, $model) {
                        return Html::a(Yii::t('app','Update'), $url, [
                            'data-pjax'=>0,
                            'data-toggle' => 'modal',    //弹框
                            'data-target'=>'#middle-modal',
                            'title' => Yii::t('app', '编辑'),
                            'class' => 'btn btn-primary btn-xs update',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a(Yii::t('app','Delete'), ['delete','name'=>'middle','id'=>$model->id], [
                            'title' => Yii::t('app', '删除此广告'),
                            'class' => 'btn btn-danger btn-xs',
                            'data-confirm' => "确认删除该广告？一旦删除将无法显示该广告",
                        ]);
                    }
                ],
            ],
        ],
        'toolbar'=> [
            ['content'=>
                $count>=5 ? '':Html::a('<i class="glyphicon glyphicon-plus">添加首页中间广告</i>', ['#'],[
                    'id'=>'middle-add',
                    'data-pjax'=>0,
                    'type'=>'button',
                    'title'=>'添加头部广告',
                    'class'=>'btn btn-primary add',
                    'data-toggle' => 'modal',    //弹框
                    'data-target' => '#middle-modal',    //指定弹框的id
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
    'id' => 'middle-modal',
    'options' => [
        'tabindex' => false
    ],
]);
\yii\bootstrap\Modal::end();
?>
<script language="JavaScript">
    $(function(){
        $(document).ready(init());
        $(document).on('pjax:complete', function() {init();});
    });
    function init() {
        $('.add').on('click', function () {  //查看详情的触发事件
            $('.ad-list-form').remove();
            $('#middle-modal').find('.modal-title').html('新增首页中间广告');
            $.get(toRoute('ad/create'), { key:'middle'  },
                function (data) {
                    $('#middle-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
            );
        });
        $('.update').on('click', function () {  //查看详情的触发事件
            $('.ad-list-form').remove();
            $('#middle-modal').find('.modal-title').html('编辑首页中间广告');
            $.get(toRoute('ad/update'), { id:$(this).closest('tr').data('key'),key:'head'  },
                function (data) {
                    $('#middle-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
            );
        });
        $('.ui-autocomplete').css('z-index','99999');
        $('.datepicker-days').css('z-index','99999');
    }
    $("#middle-modal").on("hidden.bs.modal", function () {
        $("#ad-form")[0].reset();//重置表单
    });
</script>
