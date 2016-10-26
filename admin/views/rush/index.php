<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\select2\Select2;
use dosamigos\datetimepicker\DateTimePicker;
use yii\jui\AutoComplete;
use admin\models\GoodRush;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\RushSearch $searchModel
 */

$this->title = '抢购管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-rush-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    echo GridView::widget([
        'options'=>[
            'id'=>'good_rush',
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'rush_pjax',
            ],
            'neverTimeout'=>true,
        ],
        'columns' => [
            [
                'class'=>'kartik\grid\CheckboxColumn',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'2%',
                'name'=>'id',
                'contentOptions'=>['class'=>'kartik-sheet-style'],
                'headerOptions'=>['class'=>'kartik-sheet-style']
            ],
            [
                'attribute'=>'good_name',
                'header'=>'商品名称',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'10%',
                'format'=>'html',
                'value'=>function($model) {
                    return Html::a($model->g->name.$model->g->volum,['good/view', 'id' => $model->id],
                        ['title' => '查看商品详细','class'=>'btn btn-link btn-sm']
                    );
                },
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions'=>[
                        'source'=>GoodRush::GetGoodNames(),
                    ],
                ]
            ],
            [
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=>'price',
                'width'=>'5%',
                'value'=>function($model) {
                    return '¥'.$model->price;
                },
                'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
            ],
            [
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'label'=>'库存',
                'attribute'=>'amount',
                'width'=>'5%',
                'value'=>function($model) {
                    return $model->limit.$model->g->unit;
                },
                'filterInputOptions'=>['onkeyup'=>'this.value=this.value.replace(/\D/gi,"")','class'=>'form-control'],
            ],
            [
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=>'limit',
                'label'=>'限购数',
                'width'=>'5%',
                'value'=>function($model) {
                    return $model->limit.$model->g->unit;
                },
                'filterInputOptions'=>['onkeyup'=>'this.value=this.value.replace(/\D/gi,"")','class'=>'form-control'],
            ],
            [
                'attribute'=>'start_at',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'13%',
                'headerOptions'=>['class'=>'kv-sticky-column'],
                'contentOptions'=>['class'=>'kv-sticky-column'],
                'format'=>[
                    'time',
                    (isset(Yii::$app->modules['datecontrol']['displaySettings']['time'])) ? Yii::$app->modules['datecontrol']['displaySettings']['time'] : 'h:i:s A'
                ],
                'value'=>function($model){
                    return strtotime($model->start_at);
                },
                'filterType'=>DateTimePicker::className(),
                'filterWidgetOptions'=>[
                    'value' => '',
                    // inline too, not bad
                    'inline' => false,
                    'language'=>'zh-CN',
                    'options'=>[
                        'readonly'=>true,
                    ],
                    'template'=>"{button}{reset}{input}",
                    // modify template for custom rendering
                    'clientOptions' => [
                        'autoclose' => true,
                        'format'=>'hh:ii:00',
                        'startView'=>1,
                        'maxView'=>1,
                        'keyboardNavigation'=>false,
                        'showMeridian'=>true,
                        'minuteStep'=>10,
                        'forceParse'=>false,
                        'readonly'=>true,
                    ]
                ],
                'filterInputOptions'=>['readonly'=>true],
            ],
            [
                'attribute'=>'end_at',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'13%',
                'headerOptions'=>['class'=>'kv-sticky-column'],
                'contentOptions'=>['class'=>'kv-sticky-column'],
                'format'=>[
                    'time',
                    (isset(Yii::$app->modules['datecontrol']['displaySettings']['time'])) ? Yii::$app->modules['datecontrol']['displaySettings']['time'] : 'h:i:s A'
                ],
                'value'=>function($model){
                    return strtotime($model->end_at);
                },
                'filterType'=>DateTimePicker::className(),
                'filterWidgetOptions'=>[
                    'value' => '',
                    // inline too, not bad
                    'inline' => false,
                    'language'=>'zh-CN',
                    'options'=>[
                        'readonly'=>true,
                    ],
                    'template'=>"{button}{reset}{input}",
                    // modify template for custom rendering
                    'clientOptions' => [
                        'autoclose' => true,
                        'format'=>'hh:ii:00',
                        'startView'=>1,
                        'maxView'=>1,
                        'keyboardNavigation'=>false,
                        'showMeridian'=>true,
                        'minuteStep'=>10,
                        'forceParse'=>false,
                        'readonly'=>true,
                    ]
                ],
                'filterInputOptions'=>['readonly'=>true],
            ],

            [
                'label'=>'支付方式',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=>'rush_pay',
                'width'=>'10%',
                'format'=>'raw',
                'value'=>function($model){
                    $payArr = ['1'=>'余额','2'=>'支付宝','3'=>'微信'];
                    if(empty($model->rush_pay)){
                        $html =  '<label class="label label-danger">未设置</label>';
                    }else{
                        $pay = explode('|',$model->rush_pay);
                        $html  = '';
                        foreach ($pay as $value){
                            $html.= '<label class="label label-default" style="margin-left: 2%">'.$payArr[$value].'</label>';
                        }
                    }
                    return $html;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>['1'=>'余额','2'=>'支付宝','3'=>'微信'],
                'filterWidgetOptions'=>[
                    'hideSearch'=>true,
                    'options'=>['placeholder'=>'支付方式'],
                    'pluginOptions' => ['allowClear'=>true],
                ],
            ],

            [
                'label'=>'积分支持',
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'point_sup',
                'vAlign'=>GridView::ALIGN_LEFT,
                'width'=>'7%',
                'trueLabel'=>'支 持',
                'falseLabel'=>'不支持',
                'trueIcon'=>'<label class="label label-success">支 持</label>',
                'falseIcon'=>'<label class="label label-danger">不支持</label>',
            ],

            [
                'label'=>'抢购状态',
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'is_active',
                'vAlign'=>GridView::ALIGN_LEFT,
                'width'=>'7%',
                'trueLabel'=>'上架中',
                'falseLabel'=>'已下架',
                'trueIcon'=>'<label class="label label-success">上架中</label>',
                'falseIcon'=>'<label class="label label-danger">已下架</label>',
            ],

            [
                'label'=>'原价',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'mergeHeader'=>true,
                'headerOptions'=>['class'=>'kartik-sheet-style'],
                'attribute'=>'g.price',
                'width'=>'5%',
                'value'=>function($model) {
                    return '¥'.$model->g->price;
                }
            ],
            [
                'label'=>'商品状态',
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'g.is_active',
                'vAlign'=>GridView::ALIGN_LEFT,
                'width'=>'7%',
                'mergeHeader'=>true,
                'trueIcon'=>'<label class="label label-success">上架中</label>',
                'falseIcon'=>'<label class="label label-danger">已下架</label>',
            ],
            [
                'header' => '操作',
                'class' => 'kartik\grid\ActionColumn',
                'width'=>'11%',
                'buttons' => [
                    'view'=>function ($url, $model) {
                        return '';
                    },
                    'update' => function ($url, $model) {
                        return Html::a(Yii::t('app','Update'), $url, [
                            'title' => Yii::t('app', '编辑'),
                            'class' => 'btn btn-primary btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        if($model->is_active == 0){
                            return Html::a(Yii::t('app','Up'), $url, [
                                'title' => Yii::t('app', '上架该抢购'),
                                'class' => 'btn btn-success btn-xs',
                                'data-confirm'=>'确定上架该抢购商品？',
                            ]);
                        }else{
                            return Html::a(Yii::t('app','Down'), $url, [
                                'title' => Yii::t('app', '下架该抢购'),
                                'class' => 'btn btn-danger btn-xs',
                                'data-confirm'=>'确定下架该抢购商品？',
                            ]);
                        }
                    }
                ],
            ],
        ],
        // set your toolbar
        'toolbar'=> [
            ['content'=>
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],['data-pjax'=>0,'type'=>'button', 'title'=>'发布抢购', 'class'=>'btn btn-primary']).
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
            'before'=>
                Html::a("批量上架", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'rush_up']).
                Html::a("批量下架", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'rush_down']).
                Html::a("批量积分支持", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'point_up']).
                Html::a("批量取消积分", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'point_down']),
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
        $(document).ready(rush());
        $(document).on('pjax:complete', function() {rush();});
    });
    function rush() {
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
        $('.ui-autocomplete').css('z-index','99999');

        $("#rush_up,#rush_down,#point_up,#point_down").on("click", function () {
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
            var keys = $("#good_rush").yiiGridView("getSelectedRows");
            if(keys == ''){
                layer.msg('请选择需要操作的产品',{
                    icon: 0,
                    time: 1500 //2秒关闭（如果不配置，默认是3秒）
                });
                return false;
            }
            var button = $(this).attr('id');
            var confirm = '';
            if(button == 'rush_up'){
                confirm = '确认上架产品？一旦上架用户将看到上架中的抢购产品';
            }else if(button == 'rush_down') {
                confirm = '确认下架产品？一旦下架用户将无法看到下架的抢购产品';
            }else if(button == 'point_up') {
                confirm = '确认积分支持？一旦支持用户将可以在下单时使用积分抵现';
            }else if(button == 'point_down') {
                confirm = '确认取消积分支持？一旦取消用户再下单时将无法使用积分抵现';
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
                $.post(toRoute('rush/patch'),{
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
                        $.pjax.reload({container:"#rush_pjax"});
                    }else{
                        return false;
                    }
                },'json');
            });
        });
    }
</script>
