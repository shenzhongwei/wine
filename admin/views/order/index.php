<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use admin\models\OrderInfo;
use yii\jui\AutoComplete;
use admin\models\Dics;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\OrderInfoSearch $searchModel
 */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
\admin\assets\AppAsset::register($this);
// here
$typeArr = [1=>'普通订单','2'=>'会员订单','3'=>'抢购订单'];
?>
<div class="order-info-index">
    <?php
    echo GridView::widget([
        "options" => [
            // ...其他设置项
            "id" => "order_info"
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'order_pjax',
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
            ],
            [
                'label'=>'下单时间',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=>'order_date',
                'format' => ["date", "php:Y-m-d H:i:s"],
                'width'=>'16%',
                'filterType'=>GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions'=>[
                    'presetDropdown'=>true,
                    'hideInput'=>true,
                    'language'=>'zh-CN',
                    'value'=>'',
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ],
                    ],
                ]
            ],
            [
                'header'=>'下单手机',
                'attribute'=>'username',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'7%',
                'value'=> function($model){
                    return $model->username;
                },
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions' => [
                        'source' =>OrderInfo::GetUsernames(),
                    ],
                ],
            ],
            [
                'header'=>'订单编号',
                'attribute'=>'order_code',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'8%',
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions' => [
                        'source' =>OrderInfo::GetOrderCodes(),
                    ],
                ],
            ],
            [
                'header'=>'订单类型',
                'attribute'=>'type',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'7%',
                'format'=>'html',
                'value'=>function($model){
                    $typeArr = [1=>'普通订单','2'=>'会员订单','3'=>'抢购订单'];
                    return empty($typeArr[$model->type]) ? '<span class="not-set">未知类型</span>':$typeArr[$model->type];
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>$typeArr,
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'选择类型'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],

            [
                'label'=>'商品总价',
                'attribute'=>'total',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'5%',
                'value'=>function($model){
                    return '¥'.round($model->total,2);
                },
                'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
            ],

            [
                'label'=>'支付价格',
                'attribute'=>'pay_bill',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'5%',
                'value'=>function($model){
                    return '¥'.round($model->pay_bill,2);
                },
                'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
            ],

            [
                'label'=>'优惠金额',
                'attribute'=>'disc',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'5%',
                'value'=>function($model){
                    return round($model->disc,2)==0 ? '无':'¥'.round($model->disc,2);
                },
                'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
            ],
            [
                'label'=>'用券',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'class'=>'kartik\grid\BooleanColumn',
                'trueIcon'=>'<label class="label label-success">用 券</label>',
                'falseIcon'=>'<label class="label label-danger">无 券</label>',
                'width'=>'6%',
                'attribute' => 'is_ticket',
                'trueLabel'=>'用 券',
                'falseLabel'=>'无 券',
            ],
            [
                'label'=>'积分',
                'class'=>'kartik\grid\BooleanColumn',
                'trueIcon'=>'<label class="label label-success">有积分</label>',
                'falseIcon'=>'<label class="label label-danger">无积分</label>',
                'width'=>'6%',
                'attribute' => 'is_point',
                'trueLabel'=>'有积分',
                'falseLabel'=>'无积分',
            ],
            [
                'label'=>'订单进度',
                'attribute'=>'step',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'7%',
                'value'=>function($model){
                    return OrderInfo::getOrderstep($model->state);
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>Dics::GetOrderState(),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'订单进度'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'header'=>'付款方式',
                'attribute'=>'pay_id',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'7%',
                'value'=>function($model){
                    return OrderInfo::getPaytype($model->pay_id);
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>Dics::GetPayModes(),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'支付方式'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'label'=>'状态',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'class'=>'kartik\grid\BooleanColumn',
                'trueIcon'=>'<label class="label label-success">正 常</label>',
                'falseIcon'=>'<label class="label label-danger">已删除</label>',
                'width'=>'7%',
                'attribute' => 'status',
                'trueLabel'=>'正 常',
                'falseLabel'=>'已删除',
            ],
            [
                'header' => '操作',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'13%',
                'class' =>  'kartik\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-eye">查看</i>', $url, [
                            'data-pjax'=>0,
                            'title' => '查看详细信息',
                            'class' => 'btn btn-info btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        if($model->status == 0){
                            return Html::a(Yii::t('app','Recover'), $url, [
                                'title' => Yii::t('app', '还原订单'),
                                'class' => 'btn btn-success btn-xs',
                                'data-confirm' => '确认还原该订单吗？',
                            ]);
                        }else{
                            return Html::a(Yii::t('app','Delete'), $url, [
                                'title' => Yii::t('app', '删除订单'),
                                'class' => 'btn btn-danger btn-xs',
                                'data-confirm' => '确认删除该订单吗?',
                            ]);
                        }
                    },
                    'update' => function ($url, $model) {
                         if(in_array($model->state,[2,3])){
                             if($model->state == 2){
                                 return Html::a(Yii::t('app','Receive'),['receive','id'=>$model->id], [
                                     'title' => Yii::t('app', '接单'),
                                     'class' => 'btn btn-primary btn-xs',
                                     'data-confirm' => '确定接单吗',
                                 ]);
                             }else{
                                 return Html::a(Yii::t('app','Truck'),['#'], [
                                     'title' => Yii::t('app', '发货'),
                                     'data-confirm' => '确定发货吗',
                                     'class' => 'btn btn-success btn-xs',
                                 ]);
                             }
                        }else{
                             return '';
                         }
                    },
                ],
            ],
        ],
        // set your toolbar
        'toolbar'=> [
            ['content'=>
                Html::a('一键接单', "javascript:void(0);",['id'=>'patch_receive','data-pjax'=>0,'type'=>'button', 'title'=>'一键接单', 'class'=>'btn btn-primary']).
                Html::a('<i class="glyphicon glyphicon-repeat"> 刷新</i>', ['index'], [ 'class'=>'btn btn-default', 'title'=>'刷新列表'])
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
                Html::a("批量删除", "javascript:void(0);", ["class" => "btn btn-primary",'id'=>'order_delete']).
                Html::a("批量还原", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'order_recover']).
                Html::a("批量发货", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'order_send']):'',
            'after'=>false,
            'showPanel'=>true,
            'showFooter'=>false
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
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
        $('.ui-autocomplete').css('z-index','99999');
        $('.datepicker-days').css('z-index','99999');

        $("#order_delete,#order_recover").on("click", function () {
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
            var keys = $("#order_info").yiiGridView("getSelectedRows");
            if(keys == ''){
                layer.msg('请选择需要操作的订单',{
                    icon: 0,
                    time: 1500 //2秒关闭（如果不配置，默认是3秒）
                });
                return false;
            }
            var button = $(this).attr('id');
            var confirm = '';
            if(button == 'order_delete'){
                confirm = '确认删除订单，一旦删除，将无法将此订单加入报表';
            }else if(button == 'order_recover'){
                confirm = '确认还原订单，一旦复原，该订单将加入中报表';
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
                $.post(toRoute('order/patch'),{
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
                        $.pjax.reload({container:"#order_pjax"});
                    }else{
                        return false;
                    }
                },'json');
            });
        });
        $("#patch_receive").on("click", function () {
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
            var confirm = '确认接下所有待接订单？';
            layer.confirm(confirm,{icon: 0, title:'提示'},function(index){
                layer.close(index);
                ShowLoad();
                $.post(toRoute('order/patch-receive'),{
                },function(data){
                    ShowMessage(data.status,data.message);
                    if(data.status == '302'){
                        layer.alert('登录信息已过期，请重新登录',{icon: 0},function(){
                            window.top.location.href=toRoute('site/login');
                        });
                        return false;
                    }else if(data.status == '200'){
                        $.pjax.reload({container:"#order_pjax"});
                    }else{
                        return false;
                    }
                },'json');
            });
        });
    }
</script>


