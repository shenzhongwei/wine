<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use admin\models\PromotionType;
use admin\models\PromotionInfo;
use yii\jui\AutoComplete;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\Url;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\PromotionInfoSearch $searchModel
 */

$this->title = '活动列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promotion-info-index">
    <?php
    echo GridView::widget([
        "options" => [
            // ...其他设置项
            "id" => "promotion-info"
        ],
        'dataProvider' => $dataProvider,
        'filterModel'=>$searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'promotion_info_pjax',
            ],
            'neverTimeout'=>true,
        ],
        'columns' => [
            [
                'width'=>'2%',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'class' => 'kartik\grid\CheckboxColumn',
                'name'=>'id',
            ],
            [
                'header'=>'优惠名称',
                'width'=>'8%',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'attribute'=>'name',
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions' => [
                        'source' =>PromotionInfo::GetNames(),
                    ],
                ]
            ],
            [
                'attribute'=>'pt_id',
                'header'=>'促销分类',
                'width'=>'8%',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'value'=>function($data){
                    return $data->pt->name;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>PromotionInfo::GetTypes(),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'促销分类'],
                    'pluginOptions' => ['allowClear' => true],
                ]
            ],
            [
                'attribute'=>'limit',
                'header'=>'适用范围',
                'width'=>'8%',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'format'=>'html',
                'value'=>function($data){
                    return PromotionType::getPromotionRangeById($data->limit);
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>PromotionInfo::GetLimits(),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'适用范围'],
                    'pluginOptions' => ['allowClear' => true],
                ]
            ],
            [
                'header'=>'适用对象',
                'width'=>'8%',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'attribute'=>'target_id',
                'format'=>'html',
                'value'=>function($data){
                    return PromotionInfo::getNameByRange($data);
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>PromotionInfo::GetTargets($searchModel->limit),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'适用对象','disabled'=>empty($searchModel->limit) ? true:false],
                    'pluginOptions' => ['allowClear' => true],
                ]

            ],
            [
                'attribute'=>'style',
                'width'=>'7%',
                'header'=>'优惠类型',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'format'=>'html',
                'value'=>function($data){
                    return PromotionType::getPromotionStyleById($data->style);
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>PromotionInfo::GetStyles(0),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'优惠类型'],
                    'pluginOptions' => ['allowClear' => true],
                ]
            ],
            [
                'label'=>'条件',
                'width'=>'5%',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'attribute'=>'condition',
                'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
            ],
            [
                'label'=>'优惠额',
                'width'=>'5%',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'attribute'=>'discount',
                'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
            ],
            [
                'label'=>'开始日期',
                'width'=>'11%',
                'attribute'=>'start_at',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'value'=>function($data){
                    return empty($data->start_at)?'<span class="not-set">永久有效</span>':date('Y年m月d日',$data->start_at);
                },
                'filterType'=>GridView::FILTER_DATE,
                'filterWidgetOptions'=>[
                    // inline too, not bad
                    'language' => 'zh-CN',
                    'options' => ['placeholder' => '开始日期','readonly'=>true],
                    'pluginOptions' => [
                        'format' => 'yyyy年mm月dd日',
                        'autoclose' => true,

                    ]
                ]
            ],
            [
                'label'=>'结束日期',
                'width'=>'11%',
                'attribute'=>'end_at',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'value'=>function($data){
                    return empty($data->end_at)?'<span class="not-set">永久有效</span>':date('Y年m月d日',$data->end_at);
                },
                'filterType'=>GridView::FILTER_DATE,
                'filterWidgetOptions'=>[
                    // inline too, not bad
                    'language' => 'zh-CN',
                    'options' => ['placeholder' => '结束日期','readonly'=>true],
                    'pluginOptions' => [
                        'format' => 'yyyy年mm月dd日',
                        'autoclose' => true,

                    ]
                ]
            ],

            [
                'label'=>'活动状态',
                'width'=>'7%',
                'attribute'=>'is_active',
                'vAlign'=>'middle',
                'class' => 'kartik\grid\BooleanColumn',
                'hAlign'=>'center',
                'trueIcon'=>'<label class="label label-success">上架中</label>',
                'falseIcon'=>'<label class="label label-danger">已下架</label>',
                'trueLabel'=>'上架中',
                'falseLabel'=>'已下架',
            ],

            [
                'header'=>'有效期限',
                'width'=>'5%',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'attribute'=> 'valid_circle',
                'format'=>'html',
                'value'=>function($data){
                    return empty($data->valid_circle)?'<span class="not-set">永久有效</span>':$data->valid_circle.'天';
                }
            ],
            [
                'attribute'=> 'time',
                'width'=>'5%',
                'header'=>'可用次数',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'value'=>function($data){
                    return empty($data->time)?'<span class="not-set">无限制</span>':$data->time.'次';
                }
            ],
            [
                'header'=>'操作',
                'width'=>'10%',
                'class' =>'kartik\grid\ActionColumn',
                'vAlign'=>'middle',
                'hAlign'=>'center',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return '';
//                            Html::a('<i class="fa fa-eye">查看</i>', $url, [
//                            'title' => Yii::t('app', '查看'),
//                            'class' => 'del btn btn-primary btn-xs',
//                        ]);
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
                                'title' => Yii::t('app', '上架该优惠活动'),
                                'class' => 'btn btn-success btn-xs',
                                'data-confirm' => '确认上架该优惠活动，一旦上架用户在有效期内将可参与该活动？',
                            ]);
                        }else{
                            return Html::a(Yii::t('app','Down'), $url, [
                                'title' => Yii::t('app', '下架该类型'),
                                'class' => 'btn btn-danger btn-xs',
                                'data-confirm' => '确认下架该优惠活动，一旦下架用户将不能参与？',
                            ]);
                        }
                    }

                ],
            ],
        ],
        // set your toolbar

        'toolbar'=> [
            ['content'=>
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],['data-pjax'=>0,'type'=>'button', 'title'=>'发布活动', 'class'=>'btn btn-primary']).
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
            'before'=>$dataProvider->totalCount>0 ?
                Html::a("批量上架", "javascript:void(0);", ["class" => "btn btn-primary",'id'=>'promotion_up']).
                Html::a("批量下架", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'promotion_up_down']):'',
            'after'=>false,
            'showPanel'=>true,
            'showFooter'=>true
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]); ?>

</div>
<script language="JavaScript">
    $(function(){
        $(document).ready(init());
        $(document).on('pjax:complete', function() {init();});
    });
    function init() {
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
        $('.ui-autocomplete').css('z-index','99999');
        $('.datepicker-days').css('z-index','99999');

        $("#promotion_up,#promotion_down").on("click", function () {
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
            var keys = $("#promotion-info").yiiGridView("getSelectedRows");
            if(keys == ''){
                layer.msg('请选择需要操作的类型',{
                    icon: 0,
                    time: 1500 //2秒关闭（如果不配置，默认是3秒）
                });
                return false;
            }
            var button = $(this).attr('id');
            var confirm = '';
            if(button == 'promotion'){
                confirm = '确认上架类别？一旦上架用户可参与有效的促销活动';
            }else if(button == 'promotion_down') {
                confirm = '确认下架类别？一旦下架用户将无法参与有效的促销活动';
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
                $.post(toRoute('promotion/patch'),{
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
                        $.pjax.reload({container:"#promotion_info_pjax"});
                    }else{
                        return false;
                    }
                },'json');
            });
        });
    }
</script>
