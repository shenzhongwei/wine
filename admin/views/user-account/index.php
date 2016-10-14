<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use admin\models\Dics;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\UserAccountSearch $searchModel
 */

$this->title = '账户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .modal-dialog{
        min-width:1000px;
        width:auto;;
    }
</style>
<div class="user-account-index">
    <?php Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'header'=>'序号',
                'class' => 'yii\grid\SerialColumn'
            ],
            [
                'attribute'=>'level',
                'value'=>function($data){
                    switch($data->level){
                        case 1:$str='管理员';break;
                        case 2:$str='用户';break;
                        default:$str='类别有误';break;
                    }
                    return $str;
                }
            ],
            [
                'attribute'=>'target_name',
                'format'=>'html',
                'value'=>function($data){
                    return Html::a(\admin\models\UserAccount::getAccountAcceptName($data),['view','id'=>$data->target]);
                }
            ],
            [
                'attribute'=> 'type',
                'value'=>function($data){
                    return Dics::find()->where(['type'=>'钱包类型','id'=>$data->type])->one()->name;
                }
            ],

            [
                'attribute'=> 'start',
                'class'=>'kartik\grid\EditableColumn',
                'editableOptions'=>[
                    'asPopover' => false,
                ]
            ],
            [
                'attribute'=> 'end',
                'class'=>'kartik\grid\EditableColumn',
                'editableOptions'=>[
                    'asPopover' => false,
                ]
            ],
            [
                'header'=>'操作',
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}&nbsp;&nbsp;&nbsp;{delete}',
                'buttons' => [
                    'view' => function ($url, $model,$key) {
                        return Html::a('<i class="fa fa-eye">账户明细</i>', '#',
                            [                                                  //属性
                                'data-toggle' => 'modal',    //弹框
                                'data-target' => '#pop-modal',    //指定弹框的id
                                'class' => 'del btn btn-primary btn-xs',
                               // 'class' => 'data-view',   //链接的class
                                'data-id' => $key,
                                'rel'=>'look'
                            ]
                        );
                    },
                    'delete' => function ($url, $model) {
                        if($model->is_active == 1){
                            return Html::a('<i class="fa fa-close">删除</i>',$url, [
                                'title' => Yii::t('app', '删除订单'),
                                'class' => 'del btn btn-danger btn-xs',
                                'data'=>['confirm'=>'你确定要删除该账户吗？']
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
            'before'=>$this->render('_search', ['model' => $searchModel]),
            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>刷新列表', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>

</div>
<!--查看看详情弹出框  start-->
<?php

\yii\bootstrap\Modal::begin([
    'id' => 'pop-modal',
    'header' => '<h4 class="modal-title">查看详情</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
]);
$requestUrl = \yii\helpers\Url::toRoute('view');  //当前控制器下的view方法
$Js = <<<JS
         $('.del.btn.btn-primary.btn-xs').on('click', function () {  //查看详情的触发事件

            $.get('{$requestUrl}', { id:$(this).closest('tr').data('key'),edit_type:$(this).attr('rel')  },
                function (data) {
                    $('#pop-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
             );
         });
JS;
$this->registerJs($Js);
\yii\bootstrap\Modal::end();
?>
<!--查看看详情弹出框  end-->