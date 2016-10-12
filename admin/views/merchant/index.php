<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

$this->title = '商户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="merchant-info-index">
     <?php Pjax::begin();
     echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'header'=>'序号',
                'class' => 'yii\grid\SerialColumn'
            ],
            [
                'attribute'=> 'name',
                'format'=>'html',
                'value'=>function($data){
                    return '<a href="../manager/update?id='.$data->wa_id.'">'.$data->name.'</a>';
                }
            ],
            [
                'label'=>'商户地址',
                'format'=>'html',
                'value'=>function($data){
                    return $data->province.'-'.$data->city.'-'.$data->district.' '.$data->region.$data->address;
                }
            ],
            'phone',
            [
                'attribute'=>'is_active',
                'value'=>function($data){
                    return $data->is_active==0?'否':'是';
                },
            ],

            [
                'header'=>'操作',
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-eye"> 查看</i>', $url, [
                            'title' => Yii::t('app', '查看'),
                            'class' => 'del btn btn-primary btn-xs',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fa fa-edit"> 编辑</i>', $url, [
                            'title' => Yii::t('app', '编辑'),
                            'class' => 'del btn btn-success btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        if($model->is_active == 0){
                            return Html::a('<i class="fa fa-unlock-alt"> 激活</i>', $url, [
                                'title' => Yii::t('app', '激活该商户'),
                                'data-confirm'=>'确定激活该商户?一旦激活，改商户下的产品以及门店变为可见',
                                'class' => 'del btn btn-info btn-xs',
                            ]);
                        }else{
                            return Html::a('<i class="fa fa-lock"> 冻结</i>', $url, [
                                'title' => Yii::t('app', '冻结该商户'),
                                'data-confirm'=>'确定冻结该商户?一旦冻结，改商户下的产品以及门店将不可见',
                                'class' => 'del btn btn-danger btn-xs',
                            ]);
                        }
                    }

                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>false,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'before'=>$this->render('_search', ['model' => $searchModel,'mername'=>$mername]),
            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>刷新列表', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>true
        ],
    ]);
     Pjax::end(); ?>
</div>
<script language="JavaScript">
    $(function (){
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
    });
</script>
