<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use admin\models\GoodInfo;
use yii\jui\AutoComplete;
use admin\models\MerchantInfo;
use admin\models\GoodType;
use admin\models\GoodBrand;

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
    <?php
        echo GridView::widget([
            "options" => [
                // ...其他设置项
                "id" => "good_info"
            ],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
            'headerRowOptions'=>['class'=>'kartik-sheet-style'],
            'filterRowOptions'=>['class'=>'kartik-sheet-style'],
            'pjax'=>true,  //pjax is set to always true for this demo
            'pjaxSettings'=>[
                'options'=>[
                    'id'=>'good_pjax',
                ],
                'neverTimeout'=>true,
            ],
            'columns' => [
                [
                    'class'=>'kartik\grid\CheckboxColumn',
                    'rowSelectedClass'=>GridView::TYPE_WARNING,
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'width'=>'3%',
                    'name'=>'id',
                ],
                [
                    'header'=>'商品名称',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'attribute'=>'name',
                    'format' => 'html',
                    'width'=>'11%',
                    'value'=> function($model){
                        return Html::a($model->name.$model->volum,['good/view', 'id' => $model->id],
                            ['title' => '查看商品详细','class'=>'btn btn-link btn-sm']
                        );
                    },
                    'filterType'=>AutoComplete::className(),
                    'filterWidgetOptions'=>[
                        'clientOptions' => [
                            'source' =>GoodInfo::GetGoodNames(),
                        ],
                    ]
                ],
                [
                    'header'=>'归属商户',
                    'attribute'=>'merchant',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'width'=>'8%',
                    'format' => 'html',
                    'value'=> function($model){
                        return Html::a($model->merchant0->name,['merchant/view', 'id' => $model->merchant0->id],
                            ['title' => '查看商品详细','class'=>'btn btn-link btn-sm']
                        );
                    },
                    'filterType'=>GridView::FILTER_SELECT2,
                    'filterWidgetOptions'=>[
                        'data'=>MerchantInfo::GetMerchants(),
                        'options'=>['placeholder'=>'请选择商户'],
                        'pluginOptions' => ['allowClear' => true],
                    ],
                ],
                [
                    'header'=>'类型',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'attribute'=>'type',
                    'width'=>'8%',
                    'value'=> function($model){
                        return empty($model->type0) ? '无':$model->type0->name;
                    },
                    'filterType'=>GridView::FILTER_SELECT2,
                    'filterWidgetOptions'=>[
                        'data'=>GoodType::GetTypes(),
                        'options'=>['placeholder'=>'请选择类型'],
                        'pluginOptions' => ['allowClear' => true],
                    ],
                ],
//                [
//                    'header'=>'品牌',
//                    'hAlign'=>'center',
//                    'vAlign'=>'middle',
//                    'attribute'=>'brand',
//                    'width'=>'7%',
//                    'value'=> function($model){
//                        return $model->brand0->name;
//                    },
//                    'filterType'=>GridView::FILTER_SELECT2,
//                    'filterWidgetOptions'=>[
//                        'data'=>GoodBrand::GetBrands(),
//                        'options'=>['placeholder'=>'请选择品牌'],
//                        'pluginOptions' => ['allowClear' => true],
//                    ],
//                ],
                [
                    'attribute'=>'price',
                    'label' => '原价',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'width'=>'6%',
                    'value'=> function($model){
                        return '¥'.$model->price;
                    },
                    'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
                ],
                [
                    'attribute'=>'pro_price',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'label' => '优惠价',
                    'width'=>'6%',
                    'value'=> function($model){
                        return '¥'.$model->pro_price;
                    },
                    'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
                ],
                [
                    'attribute'=>'vip_price',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'label' => '会员价',
                    'width'=>'6%',
                    'value'=> function($model){
                        return '¥'.$model->vip_price;
                    },
                    'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
                ],
                [
                    'header'=>'编号',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'attribute'=>'number',
                    'width'=>'6%',
                    'filterType'=>AutoComplete::className(),
                    'filterWidgetOptions'=>[
                        'clientOptions' => [
                            'source' =>GoodInfo::GetGoodNumbers(),
                        ],
                    ]
                ],
                [
                    'label'=>'发布时间',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'attribute'=>'regist_at',
                    'width'=>'11%',
                    'format'=>['date','php:Y年m月d日'],
                    'value'=>function($model){
                        return $model->regist_at;
                    },
                    'filterType'=>GridView::FILTER_DATE,
                    'filterWidgetOptions'=>[
                        // inline too, not bad
                        'language' => 'zh-CN',
                        'options' => ['placeholder' => '发布日期','readonly'=>true],
                        'pluginOptions' => [
                            'format' => 'yyyy年mm月dd日',
                            'autoclose' => true,

                        ]
                    ]
                ],


                [
                    'label'=>'会员显示',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'class'=>'kartik\grid\BooleanColumn',
                    'trueIcon'=>'<label class="label label-info">显 示</label>',
                    'falseIcon'=>'<label class="label label-danger">不显示</label>',
                    'width'=>'7%',
                    'attribute' => 'vip_show',
                    'trueLabel'=>'显 示',
                    'falseLabel'=>'不显示',
                ],

                [
                    'label'=>'状态',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'class'=>'kartik\grid\BooleanColumn',
                    'trueIcon'=>'<label class="label label-info">上架中</label>',
                    'falseIcon'=>'<label class="label label-danger">已下架</label>',
                    'width'=>'7%',
                    'attribute' => 'is_active',
                    'trueLabel'=>'上架中',
                    'falseLabel'=>'已下架',
                ],

//                [
//
//                    'header'=>'图片',
//                    'hAlign'=>'center',
//                    'vAlign'=>'middle',
//                    'width'=>'5%',
//                    'mergeHeader'=>true,
//                    'attribute'=>'pic',
//                    "format" => "raw",
//                    'value'=>function($model){
//                        return empty($model->pic) ? '<label class="label label-primary">暂无</label>':Html::img('../../../photo'.$model->pic,[
//                            'width'=>"50px",'height'=>"50px","onclick"=>"ShowImg(this);",'style'=>'cursor:pointer','title'=>"点击放大"
//                        ]);
//                    }
//                ],

                [
                    'header'=>'轮播图',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'class' =>  'kartik\grid\ActionColumn',
                    'width'=>'5%',
                    'buttons'=>[
                        'view' => function ($url, $model) {
                            return Html::a('点击查看', ['good/pic','id'=>$model->id], [
                                'data-pjax'=>0,
                                'class' => 'btn btn-link btn-xs',
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
                    'header'=>'详情',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'class' =>  'kartik\grid\ActionColumn',
                    'width'=>'5%',
                    'buttons'=>[
                        'view' => function ($url, $model) {
                            return Html::a('点击查看', '#', [
                                'id'=>'detail',//属性
                                'data-toggle' => 'modal',    //弹框
                                'data-target' => '#good-modal',    //指定弹框的id
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
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'width'=>'11%',
                    'class' =>  'kartik\grid\ActionColumn',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<i class="fa fa-eye">查看</i>', $url, [
                                'data-pjax'=>0,
                                'title' => Yii::t('app', Yii::t('app','View')),
                                'class' => 'btn btn-info btn-xs',
                            ]);
                        },
                        'update' => function ($url, $model) {
                            return Html::a(Yii::t('app','Update'), $url, [
                                'data-pjax'=>0,
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
            // set your toolbar
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],['data-pjax'=>0,'type'=>'button', 'title'=>'发布商品', 'class'=>'btn btn-primary']).
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], [ 'class'=>'btn btn-default', 'title'=>'刷新列表'])
                ],
                '{toggleData}',
                '{export}',
            ],
            'responsive'=>false,
            'condensed'=>true,
            'panel' => [
                'type'=>'info',
                'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
                'before'=>Html::a("批量显示", "#", ["class" => "btn btn-primary",'id'=>'vip_show']).
                    Html::a("批量不显示", "#", ["class" => "btn btn-primary",'style'=>'margin-left:10px','id'=>'vip_unshow']).
                    Html::a("批量上架", "#", ["class" => "btn btn-primary",'style'=>'margin-left:10px','id'=>'good_up']).
                    Html::a("批量下架", "#", ["class" => "btn btn-primary",'style'=>'margin-left:10px','id'=>'good_down']),
                'after'=>false,
                'showPanel'=>true,
                'showFooter'=>true
            ],
            'export'=>[
                'fontAwesome'=>true
            ],
    ]); ?>

</div>
<!--查看看详情弹出框  start-->
<?php

\yii\bootstrap\Modal::begin([
    'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    'id' => 'good-modal',
    'header' => '<h4 class="modal-title">查看详情</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
]);
\yii\bootstrap\Modal::end();
?>
<!--查看看详情弹出框  end-->
<script language="JavaScript">
    $(function(){
        $(document).ready(init());
        $(document).on('pjax:complete', function() {init();});
    });
    function init() {
        $('.detail').on('click', function () {  //查看详情的触发事件
            $.get(toRoute('good/detail'), { id:$(this).closest('tr').data('key')  },
                function (data) {
                    $('#good-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
            );
        });
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
        $('.ui-autocomplete').css('z-index','99999');
        $('.datepicker-days').css('z-index','99999');

        $("#vip_show,#vip_unshow,#good_up,#good_down").on("click", function () {
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                statusCode: {
                    302: function() {
                        layer.alert('登录信息已过期，请重新登录',{icon: 0},function(){
                            window.top.location.href=toRoute('site/login');
                        });
                        return false;
                    }
                }
            });
            var keys = $("#good_info").yiiGridView("getSelectedRows");
            if(keys == ''){
                layer.msg('请选择需要操作的产品',{
                    icon: 0,
                    time: 1500 //2秒关闭（如果不配置，默认是3秒）
                });
                return false;
            }
            var button = $(this).attr('id');
            var confirm = '';
            if(button == 'vip_show'){
                confirm = '确认显示产品？一旦显示用户将在会员列表中看到显示产品';
            }else if(button == 'vip_unshow'){
                confirm = '确认不显示产品？一旦不显示用户将在会员列表中无法看到非显示产品';
            }else if(button == 'good_up'){
                confirm = '确认上架产品？一旦上架用户将看到上架中的产品';
            }else if(button == 'good_down') {
                confirm = '确认下架产品？一旦下架用户将无法看到下架的产品';
            }else{
                layer.msg('非法操作',{
                    icon: 0,
                    time: 1500 //2秒关闭（如果不配置，默认是3秒）
                });
                return false;
            }
            layer.confirm(confirm,{icon: 0, title:'提示'},function(index){
                layer.close(index);
                ShowLoad();
                $.post(toRoute('good/patch'),{
                    'keys':keys,
                    '_wine-admin':csrfToken,
                    'button':button
                },function(data){
                    ShowMessage(data.status,data.message);
                    if(data.status == '302'){
                        layer.alert('登录信息已过期，请重新登录',{icon: 0},function(){
                            window.top.location.href=toRoute('site/login');
                        });
                        return false;
                    }else if(data.status == '200'){
                        $.pjax.reload({container:"#good_pjax"});
                    }else{
                        return false;
                    }
                },'json');
            });
        });
    }
</script>


