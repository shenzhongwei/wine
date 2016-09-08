<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use admin\models\OrderInfoSearch;
use kartik\editable\Editable;
use admin\models\EmployeeInfo;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\OrderInfoSearch $searchModel
 */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
//var_dump(EmployeeInfo::getAllemployee());exit;
?>
<div class="order-info-index">

    <?php
    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'header'=>'序号',
                'class' => 'yii\grid\SerialColumn'
            ],
            [
                'label'=>'门店',
                'attribute'=>'sid',
                'value'=>function($data){
                    return $data->s->name;
                }
            ],
            [
                'label'=>'下单用户',
                'attribute'=>'uid',
                'value'=>function($data){
                    return $data->u->nickname;
                }
            ],
            [
                'label'=>'订单地址',
                'attribute'=>'aid',
                'value'=>function($data){
                    return empty($data->aid)?'':$data->a->province.'-'.$data->a->city.'-'.$data->a->district.'-'.$data->a->region.$data->a->address;
                }
            ],
            [
                'attribute'=>'order_code',
                'value'=>function($data){
                    return $data->order_code;
                }
            ],
            [
                'attribute'=>'order_date',
                'format'=>['date','php:Y-m-d H:i:s' ],
                'value'=>function($data){
                    return $data->order_date;
                }
            ],
            [
                'label'=>'支付方式',
                'attribute'=>'pay_id',
                'value'=>function($data){
                    return empty($data->pay_id)?'未支付':OrderInfoSearch::getPaytype($data->pay_id);
                }
            ],
            'total',
            'discount',
            'ticket_id',
            'send_bill',
            [
                'label'=>'配送人员',
                'attribute'=>'send_id',
                'value'=>function($data){
                    return $data->send['name'];
                },
                'class'=>'kartik\grid\EditableColumn',
                'editableOptions'=>[
                    'format' => Editable::FORMAT_BUTTON,
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'asPopover' => true,
                    'data' =>EmployeeInfo::getAllemployee(),
                ],
            ],
            'send_code',
            'pay_bill',
            [
                'attribute'=> 'state',
                'value'=>function($data) {
                    return OrderInfoSearch::getOrderstep($data->state);
                },
                'class'=>'kartik\grid\EditableColumn',
                'editableOptions'=>[
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'asPopover' => true,
                    'data' =>EmployeeInfo::getAllorderstate(),
                ],
            ],
            [
                'attribute'=>'send_date',
                'value'=>function($data){
                    return empty($data->send_date)?'':date('Y-m-d H:i:s',$data->send_date);
                }
            ],
            [
                'attribute'=>'status',
                'format'=>'html',
                'value'=>function($data){
                    if($data->status==0 || $data->is_del==1){
                        return '<p><span class="label label-default"><i class="fa fa-times"></i> 已 删</span></p>';
                    }else{
                        return '<p><span class="label label-info"><i class="fa fa-check"></i> 正 常</span></p>';
                    }
                }
            ],
            [
                'header'=>'操作',
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}&nbsp;&nbsp;&nbsp;{delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-eye">查看</i>', $url, [
                            'title' => Yii::t('app', '查看'),
                            'class' => 'del btn btn-primary btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        if($model->status == 1){
                            return Html::a('<i>删除</i>', $url, [
                                'title' => Yii::t('app', '删除订单'),
                                'class' => 'del btn btn-danger btn-xs',
                                'data'=>['confirm'=>'你确定要删除该条记录吗？']

                            ]);
                        }
                    }
                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,

        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'before'=>$this->render('_search',['model'=>$searchModel]),
            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> 刷新列表', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]);
    Pjax::end();
    ?>

</div>
