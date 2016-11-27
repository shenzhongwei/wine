<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\jui\AutoComplete;
use admin\models\MerchantInfo;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \admin\models\MerchantInfoSearch $searchModel
 */

$this->title = '商户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-info-index">
    <?php
    echo GridView::widget([
        "options" => [
            // ...其他设置项
            "id" => "merchant_info"
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'merchant_pjax',
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
                'attribute'=>'name',
                'width'=>'12%',
                'header'=>'商户名',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions' => [
                        'source' =>MerchantInfo::GetNames(),
                    ],
                ]
            ],
            [
                'header'=>'联系人',
                'width'=>'8%',
                'attribute'=>'contacter',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions' => [
                        'source' =>MerchantInfo::GetContacters(),
                    ],
                ]
            ],
            [
                'header'=>'联系电话',
                'width'=>'8%',
                'attribute'=>'phone',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions' => [
                        'source' =>MerchantInfo::GetPhones(),
                    ],
                ],
                'filterInputOptions'=>['onkeyup'=>'this.value=this.value.replace(/\D/gi,"")','class'=>'form-control'],
            ],
            [
                'header'=>'所在城市',
                'attribute'=>'city',
                'width'=>'8%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'format'=>'html',
                'value'=>function($data){
                    return empty($data->city) ? '<span class="not-set">未设置</span>':$data->city;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>MerchantInfo::GetCities(),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'选择城市'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'header'=>'所在区域',
                'attribute'=>'district',
                'width'=>'8%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'format'=>'html',
                'value'=>function($data){
                    return empty($data->district) ? '<span class="not-set">未设置</span>':$data->district;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>MerchantInfo::GetDistricts(),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'选择地区'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'header'=>'所在街道',
                'attribute'=>'region',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'format'=>'html',
                'value'=>function($data){
                    return empty($data->region) ? '<span class="not-set">未设置</span>':$data->region;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>MerchantInfo::GetRegions(),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'选择街道'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'label'=>'管理员状态',
                'hAlign'=>'center',
                'width'=>'8%',
                'vAlign'=>'middle',
                'class'=>'kartik\grid\BooleanColumn',
                'falseIcon'=>'<label class="label label-success">正 常</label>',
                'trueIcon'=>'<label class="label label-danger">冻 结</label>',
                'attribute' => 'wa_status',
                'trueLabel'=>'冻 结',
                'falseLabel'=>'正 常',
            ],
            [
                'label'=>'商户状态',
                'hAlign'=>'center',
                'width'=>'8%',
                'vAlign'=>'middle',
                'class'=>'kartik\grid\BooleanColumn',
                'trueIcon'=>'<label class="label label-success">正 常</label>',
                'falseIcon'=>'<label class="label label-danger">冻 结</label>',
                'attribute' => 'is_active',
                'trueLabel'=>'正 常',
                'falseLabel'=>'冻 结',
            ],
            [
                'label'=>'发布时间',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=>'registe_at',
                'width'=>'14%',
                'format'=>['date','php:Y年m月d日'],
                'value'=>function($model){
                    return $model->registe_at;
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
                'header'=>'操作',
                'class' => 'kartik\grid\ActionColumn',
                'width'=>'14%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-eye">查看</i>', $url, [
                            'data-pjax'=>0,
                            'title' => '查看详细信息',
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
                        if($model->is_active == 1){
                            return Html::a(Yii::t('app', 'Lock'), $url, [
                                'title' => Yii::t('app', 'lock'),
                                'class' => 'del btn btn-warning btn-xs',
                                'data'=>['confirm'=>'确定冻结该商户，一旦冻结，用户将无法看到该商户的商品']
                            ]);
                        }elseif ($model->is_active == 0){
                            return Html::a(Yii::t('app', 'Unlock'), $url, [
                                'title' => Yii::t('app', 'unlock'),
                                'class' => 'del btn btn-info btn-xs',
                                'data'=>['confirm'=>Yii::t('app', '确定解除冻结，一旦接触，用户将可以购买该商户的商品')]

                            ]);
                        }
                    }

                ],
            ],
        ],
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
            'before'=>$dataProvider->totalCount>0 ?
                Html::a("批量冻结", "javascript:void(0);", ["class" => "btn btn-primary",'id'=>'merchant_down']).
                Html::a("批量解冻", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'merchant_up']).
                Html::a("批量恢复后台", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'merchant_unlock']).
                Html::a("批量冻结后台", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'merchant_lock']):'',
            'after'=>false,
            'showPanel'=>true,
            'showFooter'=>true
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]);  ?>

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

        $("#merchant_up,#merchant_down,#merchant_lock,#merchant_unlock").on("click", function () {
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
            var keys = $("#merchant_info").yiiGridView("getSelectedRows");
            if(keys == ''){
                layer.msg('请选择需要操作的门店',{
                    icon: 0,
                    time: 1500 //2秒关闭（如果不配置，默认是3秒）
                });
                return false;
            }
            var button = $(this).attr('id');
            var confirm = '';
            if(button == 'merchant_up'){
                confirm = '确认解除冻结？一旦解除用户将看到商户下的产品';
            }else if(button == 'merchant_down') {
                confirm = '确认冻结商户？一旦冻结用户将无法购买商户下的产品';
            }else if(button == 'merchant_unlock') {
                confirm = '确认解除锁定，一旦解除，商户将能够登陆后台';
            }else if(button == 'merchant_lock') {
                confirm = '确认锁定？一旦锁定，商户将无法登录后台';
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
                $.post(toRoute('merchant/patch'),{
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
                        $.pjax.reload({container:"#merchant_pjax"});
                    }else{
                        return false;
                    }
                },'json');
            });
        });
    }
</script>
