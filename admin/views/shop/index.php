<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use admin\models\MerchantInfoSearch;
use yii\jui\AutoComplete;
use admin\models\ShopInfo;
use kartik\select2\Select2;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\ShopSearch $searchModel
 */

$this->title = '门店列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-info-index">
    <?php
    echo GridView::widget([
        "options" => [
            // ...其他设置项
            "id" => "shop_info"
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'shop_pjax',
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
                'width'=>'11%',
                'header'=>'店名',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions' => [
                        'source' =>ShopInfo::GetNames(),
                    ],
                ]
            ],
            [
                'header'=>'所属商户',
                'width'=>'11%',
                'attribute'=>'merchant',
                'format'=>'html',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=>function($data){
                    return empty($data->merchant) ? '<span class="not-set">未设置</span>':MerchantInfoSearch::getOneMerchant($data->merchant);
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>ShopInfo::GetMerchants(),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'选择商户'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'header'=>'联系人',
                'width'=>'7%',
                'attribute'=>'contacter',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions' => [
                        'source' =>ShopInfo::GetContacters(),
                    ],
                ]
            ],
            [
                'header'=>'联系电话',
                'width'=>'7%',
                'attribute'=>'phone',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions' => [
                        'source' =>ShopInfo::GetPhones(),
                    ],
                ],
                'filterInputOptions'=>['onkeyup'=>'this.value=this.value.replace(/\D/gi,"")','class'=>'form-control'],
            ],
            [
                'label'=>'配送范围(米)',
                'attribute'=>'limit',
                'width'=>'7%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
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
                'filter'=>ShopInfo::GetCities(),
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
                'filter'=>ShopInfo::GetDistricts(),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'选择地区'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'header'=>'所在街道',
                'attribute'=>'region',
                'width'=>'12%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'format'=>'html',
                'value'=>function($data){
                    return empty($data->region) ? '<span class="not-set">未设置</span>':$data->region;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>ShopInfo::GetRegions(),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'选择街道'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'label'=>'管理员状态',
                'hAlign'=>'center',
                'width'=>'7%',
                'vAlign'=>'middle',
                'class'=>'kartik\grid\BooleanColumn',
                'falseIcon'=>'<label class="label label-success">正 常</label>',
                'trueIcon'=>'<label class="label label-danger">冻 结</label>',
                'attribute' => 'wa_status',
                'trueLabel'=>'冻 结',
                'falseLabel'=>'正 常',
            ],
            [
                'label'=>'门店状态',
                'hAlign'=>'center',
                'width'=>'7%',
                'vAlign'=>'middle',
                'class'=>'kartik\grid\BooleanColumn',
                'trueIcon'=>'<label class="label label-success">正 常</label>',
                'falseIcon'=>'<label class="label label-danger">冻 结</label>',
                'attribute' => 'is_active',
                'trueLabel'=>'正 常',
                'falseLabel'=>'冻 结',
            ],
            [
                'header'=>'操作',
                'class' => 'kartik\grid\ActionColumn',
                'width'=>'13%',
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
                                'data'=>['confirm'=>'确定冻结该店铺，一旦冻结，用户将无法看到该店铺的商品']
                            ]);
                        }elseif ($model->is_active == 0){
                            return Html::a(Yii::t('app', 'Unlock'), $url, [
                                'title' => Yii::t('app', 'unlock'),
                                'class' => 'del btn btn-info btn-xs',
                                'data'=>['confirm'=>Yii::t('app', '确定解除冻结，一旦接触，用户将可以购买该店铺的商品')]

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
                Html::a("批量冻结", "javascript:void(0);", ["class" => "btn btn-primary",'id'=>'shop_down']).
                Html::a("批量解冻", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'shop_up']).
                Html::a("批量恢复后台", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'shop_unlock']).
                Html::a("批量冻结后台", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'shop_lock']):'',
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

        $("#shop_up,#shop_down,#shop_lock,#shop_unlock").on("click", function () {
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
            var keys = $("#shop_info").yiiGridView("getSelectedRows");
            if(keys == ''){
                layer.msg('请选择需要操作的门店',{
                    icon: 0,
                    time: 1500 //2秒关闭（如果不配置，默认是3秒）
                });
                return false;
            }
            var button = $(this).attr('id');
            var confirm = '';
            if(button == 'shop_up'){
                confirm = '确认解除冻结？一旦解除用户将看到门店中的产品';
            }else if(button == 'shop_down') {
                confirm = '确认冻结门店？一旦冻结用户将无法购买门店中的产品';
            }else if(button == 'shop_unlock') {
                confirm = '确认解除锁定，一旦解除，门店将能够登陆后台';
            }else if(button == 'shop_lock') {
                confirm = '确认锁定？一旦锁定，门店将无法登录后台';
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
                $.post(toRoute('shop/patch'),{
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
                        $.pjax.reload({container:"#shop_pjax"});
                    }else{
                        return false;
                    }
                },'json');
            });
        });
    }
</script>
