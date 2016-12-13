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
$admin = Yii::$app->user->identity;
if($admin->wa_type>3){
    $colum = [
        [
            'class'=>'kartik\grid\CheckboxColumn',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'1%',
            'name'=>'id',
        ],
        [
            'header'=>'订单编号',
            'attribute'=>'order_code',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'8%',
            'format' => 'raw',
            'value'=> function($model){
                $ordertable=Html::a($model->order_code,['order/view', 'id' => $model->id],
                    ['title' => '查看订单详细','class'=>' btn-link btn-sm']
                );
                if($model->state>=2 && $model->state<=7){
                    $shop_name = empty($model->s) ? '数据丢失':$model->s->name;
                    $shop_region = empty($model->s) ? '数据丢失':$model->s->region;
                    $shop_phone = empty($model->s) ? '数据丢失':$model->s->phone;
                    $shop_address = empty($model->s) ? '数据丢失':$model->s->address;
                    $payArr = [1=>'余额支付','2'=>'支付宝支付','3'=>'微信支付'];
                    $pay = empty($payArr[$model->pay_id]) ? '未知':$payArr[$model->pay_id];
                    $get_phone = empty($model->a) ? '数据丢失':$model->a->get_person.' '.$model->a->get_phone;
                    $address = empty($model->a) ? '数据丢失':$model->a->province.$model->a->city.$model->a->district.$model->a->region.$model->a->address;
                    $discount = empty(((double)$model->discount+(double)$model->point)-0) ? '未使用优惠':((double)$model->discount+(double)$model->point).'元';
                    $ordertable .= "<div class='wine-wrap' id='model$model->id'>
        <p class='p-title'>$shop_name</p>
        <div class='wine-title clearfix'>$shop_region<span class='fr'>$shop_phone</span>
        </div>
        <p class='addre'>地址：$shop_address</p>
        <div class='bordbblue'></div>";
                    $ordertable .= "<table class='wine-det'>
            <tr>
                <th valign='top'>订单编号：</th>
                <td valign='top'>$model->order_code </td>
            </tr>
            <tr>
                <th valign='top'>购买时间：</th>
                <td valign='top'>".date('Y-m-d H:i:s',$model->order_date)."</td>
            </tr>
            <tr>
                <th valign='top'>接收人：</th>
                <td valign='top'>$get_phone</td>

            </tr>
            <tr>
                <th valign='top'>配送地址：</th>
                <td valign='top'>$address</td>
            </tr>
            <tr>
                <th valign='top'>优惠额度：</th>
                <td valign='top'>$discount</td>
            </tr>
        </table>
        <div class='bordbblue'></div><table class='wine-price'>
            <tr>
                <th valign='top'>商品名称</th>
                <th valign='top'>数量</th>
                <th valign='top'>单价</th>
                <th valign='top'>金额</th>
            </tr>
            <tr>";
                    if(empty($model->orderDetails)){
                        $ordertable.= "
                <td valign='top'>丢失</td>
                <td valign='top'>丢失</td>
                <td valign='top'>丢失</td>
                <td valign='top'>丢失</td>";
                    }else{
                        foreach($model->orderDetails as $detail){
                            if(empty($detail->g)){
                                $ordertable.= "
                <td valign='top'>丢失</td>
                <td valign='top'>".$detail->amount."</td>
                <td valign='top'>".$detail->single_price."</td>
                <td valign='top'>".$detail->total_price."</td>";
                            }else{
                                $ordertable.= "
                <td valign='top'>".$detail->g->name.$detail->g->volum."</td>
                <td valign='top'>".$detail->amount."</td>
                <td valign='top'>".$detail->single_price."</td>
                <td valign='top'>".$detail->total_price."</td>";
                            }
                        }
                    }
                    $ordertable.="</tr>
<tr>
    <td colspan='4' style='text-align: right;'>合计：$model->total</td>
</tr>
</table>
<div class='bordbblue'></div>
<table class='wine-det'>
    <tr>
        <th valign='top'>支付方式：</th>
        <td valign='top'>$pay</td>
    </tr>
</table>
<p class='tips'>尊敬的客户：您签收时，请核对商品数量金额无误后签字。即日起当月内凭小票换取发票。</p></div>";
                }
                return $ordertable;
            },
            'filterType'=>AutoComplete::className(),
            'filterWidgetOptions'=>[
                'clientOptions' => [
                    'source' =>OrderInfo::GetOrderCodes(),
                ],
            ],
        ],
        [
            'label'=>'下单时间',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'attribute'=>'order_date',
            'format' => ["date", "php:Y-m-d H:i:s"],
            'width'=>'9%',
            'filterType'=>GridView::FILTER_DATE,
            'filterWidgetOptions'=>[
                // inline too, not bad
                'language' => 'zh-CN',
                'options' => ['placeholder' => '','readonly'=>true],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,

                ]
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
                'options'=>['placeholder'=>''],
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
            'width'=>'7%',
            'attribute' => 'is_point',
            'trueLabel'=>'有积分',
            'falseLabel'=>'无积分',
        ],

        [
            'label'=>'订单进度',
            'attribute'=>'step',
            'hAlign'=>'center',
            'format'=>'raw',
            'vAlign'=>'middle',
            'width'=>'6%',
            'value'=>function($model){
                return OrderInfo::getOrderstep($model->state);
            },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>Dics::GetOrderState(),
            'filterWidgetOptions'=>[
                'options'=>['placeholder'=>''],
                'pluginOptions' => ['allowClear' => true],
            ],
        ],
        [
            'header'=>'付款方式',
            'attribute'=>'pay_id',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'5%',
            'value'=>function($model){
                return OrderInfo::getPaytype($model->pay_id);
            },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>Dics::GetPayModes(),
            'filterWidgetOptions'=>[
                'options'=>['placeholder'=>''],
                'pluginOptions' => ['allowClear' => true],
            ],
        ],

        [
            'label'=>'状态',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'class'=>'kartik\grid\BooleanColumn',
            'trueIcon'=>'<label class="label label-success">正 常</label>',
            'falseIcon'=>'<label class="label label-danger">删 除</label>',
            'width'=>'6%',
            'attribute' => 'status',
            'trueLabel'=>'正 常',
            'falseLabel'=>'删 除',
        ],

        [
            'header'=>'收货地址',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'5%',
            'format'=>'raw',
            'value' => function ($model) {
                return Html::a("<i class='fa fa-map-marker'> 查看</i>", '#', [
                    'data-toggle' => 'modal',    //弹框
                    'data-target' => '#locate-modal',    //指定弹框的id
                    'class' => 'btn-link btn-xs locate',
                    'data-id' => $model->id,
                ]);
            },
        ],

        [
            'header' => '操作',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'18%',
            'template' => '{view} {update} {delete} {print}',
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
                    if(in_array($model->state,[2,3,4])){
                        if($model->state == 2){
                            return Html::a(Yii::t('app','Receive'),['receive','id'=>$model->id], [
                                'title' => Yii::t('app', '接单'),
                                'class' => 'btn btn-primary btn-xs',
                                'data-confirm' => '确定接单吗',
                            ]);
                        }elseif($model->state == 3){
                            return Html::a(Yii::t('app','Truck'),['#'], [
                                'title' => Yii::t('app', '发货'),
                                'class' => 'btn btn-success btn-xs send',
                                'data-toggle' => 'modal',    //弹框
                                'data-target' => '#send-modal',    //指定弹框的id
                                'data-id' => $model->id,
                            ]);
                        }else{
                            return Html::a(Yii::t('app','Arrive'),['arrive','id'=>$model->id], [
                                'title' => Yii::t('app', '已送达'),
                                'class' => 'btn btn-default btn-xs',
                                'data-confirm' => '确定已送达吗',
                            ]);
                        }
                    }else{
                        return '';
                    }
                },
                'print'=>function ($url,$model) {
                    if($model->state>=2 && $model->state<=7){
                        return Html::button(Yii::t('app','Print'), [
                            'class' => 'btn btn-default btn-xs print',
                        ]);
                    }else{
                        return '';
                    }
                }
            ],
        ],
    ];
}else{
    $colum = [
        [
            'class'=>'kartik\grid\CheckboxColumn',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'1%',
            'name'=>'id',
        ],
        [
            'header'=>'订单编号',
            'attribute'=>'order_code',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'9%',
            'format' => 'raw',
            'value'=> function($model){
                $ordertable=Html::a($model->order_code,['order/view', 'id' => $model->id],
                    ['title' => '查看订单详细','class'=>' btn-link btn-sm']
                );
                if($model->state>=2 && $model->state<=7){
                    $shop_name = empty($model->s) ? '数据丢失':$model->s->name;
                    $shop_region = empty($model->s) ? '数据丢失':$model->s->region;
                    $shop_phone = empty($model->s) ? '数据丢失':$model->s->phone;
                    $shop_address = empty($model->s) ? '数据丢失':$model->s->address;
                    $payArr = [1=>'余额支付','2'=>'支付宝支付','3'=>'微信支付'];
                    $pay = empty($payArr[$model->pay_id]) ? '未知':$payArr[$model->pay_id];
                    $get_phone = empty($model->a) ? '数据丢失':$model->a->get_person.' '.$model->a->get_phone;
                    $address = empty($model->a) ? '数据丢失':$model->a->province.$model->a->city.$model->a->district.$model->a->region.$model->a->address;
                    $discount = empty(((double)$model->discount+(double)$model->point)-0) ? '未使用优惠':((double)$model->discount+(double)$model->point).'元';
                    $ordertable .= "<div class='wine-wrap' id='model$model->id'>
        <p class='p-title'>$shop_name</p>
        <div class='wine-title clearfix'>$shop_region<span class='fr'>$shop_phone</span>
        </div>
        <p class='addre'>地址：$shop_address</p>
        <div class='bordbblue'></div>";
                    $ordertable .= "<table class='wine-det'>
            <tr>
                <th valign='top'>订单编号：</th>
                <td valign='top'>$model->order_code </td>
            </tr>
            <tr>
                <th valign='top'>购买时间：</th>
                <td valign='top'>".date('Y-m-d H:i:s',$model->order_date)."</td>
            </tr>
            <tr>
                <th valign='top'>接收人：</th>
                <td valign='top'>$get_phone</td>

            </tr>
            <tr>
                <th valign='top'>配送地址：</th>
                <td valign='top'>$address</td>
            </tr>
            <tr>
                <th valign='top'>优惠额度：</th>
                <td valign='top'>$discount</td>
            </tr>
        </table>
        <div class='bordbblue'></div><table class='wine-price'>
            <tr>
                <th valign='top'>商品名称</th>
                <th valign='top'>数量</th>
                <th valign='top'>单价</th>
                <th valign='top'>金额</th>
            </tr>
            <tr>";
                    if(empty($model->orderDetails)){
                        $ordertable.= "<td valign='top'>丢失</td>
                <td valign='top'>丢失</td>
                <td valign='top'>丢失</td>
                <td valign='top'>丢失</td>";
                    }else{
                        foreach($model->orderDetails as $detail){
                            if(empty($detail->g)){
                                $ordertable.= "<td valign='top'>丢失</td>
                <td valign='top'>".$detail->amount."</td>
                <td valign='top'>".$detail->single_price."</td>
                <td valign='top'>".$detail->total_price."</td>";
                            }else{
                                $ordertable.= "<td valign='top'>".$detail->g->name.$detail->g->volum."</td>
                <td valign='top'>".$detail->amount."</td>
                <td valign='top'>".$detail->single_price."</td>
                <td valign='top'>".$detail->total_price."</td>";
                            }
                        }
                    }
                    $ordertable.="</tr>
<tr>
    <td colspan='4' style='text-align: right;'>合计：$model->total</td>
</tr>
</table>
<div class='bordbblue'></div>
<table class='wine-det'>
    <tr>
        <th valign='top'>支付方式：</th>
        <td valign='top'>$pay</td>
    </tr>
</table>
<p class='tips'>尊敬的客户：您签收时，请核对商品数量金额无误后签字。即日起当月内凭小票换取发票。</p></div>";
                }
                return $ordertable;
            },
            'filterType'=>AutoComplete::className(),
            'filterWidgetOptions'=>[
                'clientOptions' => [
                    'source' =>OrderInfo::GetOrderCodes(),
                ],
            ],
        ],
        [
            'label'=>'下单时间',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'attribute'=>'order_date',
            'format' => ["date", "php:Y-m-d H:i:s"],
            'width'=>'10%',
            'filterType'=>GridView::FILTER_DATE,
            'filterWidgetOptions'=>[
                // inline too, not bad
                'language' => 'zh-CN',
                'options' => ['placeholder' => '','readonly'=>true],
                'pluginOptions' => [
                    'format' => 'yyyy年mm月dd日',
                    'autoclose' => true,

                ]
            ]
        ],
        [
            'header'=>'下单手机',
            'attribute'=>'username',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'8%',
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
            'header'=>'订单类型',
            'attribute'=>'type',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'8%',
            'format'=>'html',
            'value'=>function($model){
                $typeArr = [1=>'普通订单','2'=>'会员订单','3'=>'抢购订单'];
                return empty($typeArr[$model->type]) ? '<span class="not-set">未知类型</span>':$typeArr[$model->type];
            },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>$typeArr,
            'filterWidgetOptions'=>[
                'options'=>['placeholder'=>''],
                'pluginOptions' => ['allowClear' => true],
            ],
        ],
        [
            'label'=>'下单门店',
            'attribute'=>'sid',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'8%',
            'format'=>'raw',
            'value'=>function($model){
                return empty($model->s->name) ? '<span class="not-set">未设置</span>':
                    Html::a($model->s->name,['shop/view', 'id' => $model->sid],
                        ['title' => '查看门店信息','class'=>' btn-link btn-sm']
                    );
            },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>OrderInfo::getShopNames(),
            'filterWidgetOptions'=>[
                'options'=>['placeholder'=>''],
                'pluginOptions' => ['allowClear' => true],
            ],
        ],

        [
            'label'=>'支付价格',
            'attribute'=>'pay_bill',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'6%',
            'value'=>function($model){
                return '¥'.round($model->pay_bill,2);
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
            'width'=>'7%',
            'attribute' => 'is_ticket',
            'trueLabel'=>'用 券',
            'falseLabel'=>'无 券',
        ],

        [
            'label'=>'积分',
            'class'=>'kartik\grid\BooleanColumn',
            'trueIcon'=>'<label class="label label-success">有积分</label>',
            'falseIcon'=>'<label class="label label-danger">无积分</label>',
            'width'=>'7%',
            'attribute' => 'is_point',
            'trueLabel'=>'有积分',
            'falseLabel'=>'无积分',
        ],

        [
            'label'=>'订单进度',
            'attribute'=>'step',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'format'=>'raw',
            'width'=>'6%',
            'value'=>function($model){
                return OrderInfo::getOrderstep($model->state);
            },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>Dics::GetOrderState(),
            'filterWidgetOptions'=>[
                'options'=>['placeholder'=>''],
                'pluginOptions' => ['allowClear' => true],
            ],
        ],

        [
            'header'=>'付款方式',
            'attribute'=>'pay_id',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'6%',
            'value'=>function($model){
                return OrderInfo::getPaytype($model->pay_id);
            },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>Dics::GetPayModes(),
            'filterWidgetOptions'=>[
                'options'=>['placeholder'=>''],
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
            'falseLabel'=>'删 除',
        ],
        [
            'header' => '操作',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'17%',
            'class' =>  'kartik\grid\ActionColumn',
            'template' => '{view} {update} {delete} {print}',
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
                    if(in_array($model->state,[2,3,4])){
                        if($model->state == 2){
                            return Html::a(Yii::t('app','Receive'),['receive','id'=>$model->id], [
                                'title' => Yii::t('app', '接单'),
                                'class' => 'btn btn-primary btn-xs',
                                'data-confirm' => '确定接单吗',
                            ]);
                        }elseif($model->state == 3){
                            return Html::a(Yii::t('app','Truck'),['#'], [
                                'title' => Yii::t('app', '发货'),
                                'class' => 'btn btn-success btn-xs send',
                                'data-toggle' => 'modal',    //弹框
                                'data-target' => '#send-modal',    //指定弹框的id
                                'data-id' => $model->id,
                            ]);
                        }else{
                            return Html::a(Yii::t('app','Arrive'),['arrive','id'=>$model->id], [
                                'title' => Yii::t('app', '已送达'),
                                'class' => 'btn btn-default btn-xs',
                                'data-confirm' => '确定已送达吗',
                            ]);
                        }
                    }else{
                        return '';
                    }
                },
                'print'=>function ($url,$model) {
                    if($model->state>=2 && $model->state<=7){
                        return Html::button(Yii::t('app','Print'), [
                            'class' => 'btn btn-default btn-xs print',
                        ]);
                    }else{
                        return '';
                    }
                }
            ],
        ],
    ];
}
?>
<?=Html::jsFile('@web/js/wine/jquery.PrintArea.js')?>
<?=Html::cssFile('@web/css/wine/order.css')?>
<?=Html::cssFile('@web/css/wine/print.css')?>
<div class="order-info-index">
    <?php
    echo GridView::widget([
        "options" => [
            // ...其他设置项
            "id" => "order_info"
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'order_pjax',
            ],
            'neverTimeout'=>true,
        ],
        'columns' => $colum,
        // set your toolbar
        'toolbar'=> [
            ['content'=>$dataProvider->totalCount>0 ?
                Html::a('一键接单', "javascript:void(0);",['id'=>'patch_receive','data-pjax'=>0,'type'=>'button', 'title'=>'一键接单', 'class'=>'btn btn-primary']).
                Html::a('<i class="glyphicon glyphicon-repeat"> 刷新</i>', ['index'], [ 'class'=>'btn btn-default', 'title'=>'刷新列表']):''
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
                Html::a("批量还原", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'order_recover'])
                .Html::a("批量发货", ['#'], [
                    "class" => "btn btn-primary patch_send",'style'=>'margin-left:0.1%',
                    'data-toggle' => 'modal',    //弹框
                    'data-target' => '#send-modal',    //指定弹框的id
                ]).Html::a("批量送达", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'order_arrive'])
                . Html::a("批量打印", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'order_print'])
                :'',
            'after'=>false,
            'showPanel'=>true,
            'showFooter'=>false
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]); ?>

</div>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=<?= Yii::$app->params['key'] ?>"></script>
<style>
    .modal-header{
        text-align: center;
    }

    #locate-modal .modal-body {
        height: 500px;
        padding: 0px;
    }

    #send-modal .modal-footer {
        text-align: center;
    }

    #locate {
        height: 100%;
    }

    .info {
        border: solid 1px silver;
    }
    div.info-top {
        position: relative;
        background: none repeat scroll 0 0 #F9F9F9;
        border-bottom: 1px solid #CCC;
        border-radius: 5px 5px 0 0;
    }
    div.info-top div {
        display: inline-block;
        color: #333333;
        font-size: 14px;
        font-weight: bold;
        line-height: 31px;
        padding: 0 10px;
    }
    div.info-top img {
        position: absolute;
        top: 10px;
        right: 10px;
        transition-duration: 0.25s;
    }
    div.info-top img:hover {
        box-shadow: 0px 0px 5px #000;
    }
    div.info-middle {
        font-size: 12px;
        padding: 6px;
        line-height: 20px;
    }
    div.info-bottom {
        height: 0px;
        width: 100%;
        clear: both;
        text-align: center;
    }
    div.info-bottom img {
        position: relative;
        z-index: 104;
    }
    .loc {
        margin-left: 5px;
        font-size: 11px;
    }
    .info-middle img {
        float: left;
        margin-right: 6px;
    }
</style>
<!--查看看详情弹出框  start-->
<?php

\yii\bootstrap\Modal::begin([
    'id' => 'send-modal',
    'header' => '<h4 class="modal-title">订单发配</h4><small>请选择已装箱的订单进行配送，否则无法发起配送</small>',
    'footer' =>
        '<button class="btn btn-primary" data-dismiss="modal">关 闭</button>',
]);
\yii\bootstrap\Modal::end();

\yii\bootstrap\Modal::begin([
    'id' => 'locate-modal',
    'header' => '<h4 class="modal-title">高德地图</h4>',
    'footer' =>
        '<button class="btn btn-primary" data-dismiss="modal">关 闭</button>',
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
        $('.print').on('click', function () {
            var print = $(this).closest('tr').find(".wine-wrap");
            $( print ).printArea();
            return false;
        });
        $('.locate').on('click', function () {  //查看详情的触发事件
            $.post(toRoute('order/locate'), {id: $(this).closest('tr').data('key')},
                function (data) {
                    $('#locate-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
            );
        });
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
        $('.ui-autocomplete').css('z-index','99999');
        $('.datepicker-days').css('z-index','99999');
        $('.send').on('click', function () {  //查看详情的触发事件
            var key = $(this).closest('tr').data('key');
            $('.send-list-form').remove();
            $.post(toRoute('order/send'), { id:key,key:'single'  },
                function (data) {
                    $('#send-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
            );
        });
        $('.patch_send').on('click', function () {  //查看详情的触发事件
            var keys = $("#order_info").yiiGridView("getSelectedRows");
            if(keys == ''){
                layer.msg('请选择需要操作的订单',{
                    icon: 0,
                    time: 1500 //2秒关闭（如果不配置，默认是3秒）
                });
                return false;
            }
            $('.send-list-form').remove();
            $.post(toRoute('order/send'), { id:keys,key:'patch'  },
                function (data) {
                    $('#send-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
            );
        });
        $("#middle-modal").on("hidden.bs.modal", function () {
            $("#ad-form")[0].reset();//重置表单
        });

        $("#order_delete,#order_recover,#order_arrive").on("click", function () {
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
                confirm = '确认删除订单，一旦删除，将无法将此订单加入报表？';
            }else if(button == 'order_recover'){
                confirm = '确认还原订单，一旦复原，该订单将加入中报表？';
            }else if(button == 'order_arrive'){
                confirm = '确认订单已送达？';
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
        $("#order_print").on("click",function () {
            var print = '';
            $('.kv-row-checkbox:checked').each(function(){
                var order = $(this).closest('tr').find(".wine-wrap");
                if(typeof(order.html()) == "undefined"){
                    return true;
                }else{
                    print += (print.length > 0 ? "," : "") + "div#" + order.attr('id');
                }
            });
            if(print.length>0){
                $( print ).printArea();
            }else{
                layer.msg('请选择可打印的订单(可打印订单为可付款订单)',{
                    icon: 0,
                    time: 1500 //2秒关闭（如果不配置，默认是3秒）
                });
                return false;
            }
            return false;
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


