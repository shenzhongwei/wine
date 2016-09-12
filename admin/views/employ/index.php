<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use admin\models\EmployeeInfoSearch;
use kartik\editable\Editable;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\EmployeeInfoSearch $searchModel
 */

$this->title = '配送人员列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-info-index">

    <?php
    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'header'=>'序号',
                'class' => 'yii\grid\SerialColumn'
            ],
            'name',
            'phone',
            [
                'attribute'=>'type',
                'value'=>function($data){
                    return empty($data->type)?'商家':'门店';
                }
            ],

            [
                'label'=>'所属商家/门店名称',
                'attribute'=>'owner_id',
                'format'=>'html',
                'value'=>function($data){
                   return EmployeeInfoSearch::getOwnerName($data);
                }
            ],
            [
                'attribute'=>'status',
                'format'=>'html',
                'value'=>function($data){
                    return EmployeeInfoSearch::getEmploySattus($data);
                },
                'class'=>'kartik\grid\EditableColumn',
                'editableOptions'=>[

                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'asPopover' => true,
                    'data' =>['0'=>'删除','1'=>'正常','2'=>'繁忙','3'=>'下岗'],
                ],
            ],
            [
                'header'=>'操作',
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}&nbsp;&nbsp;&nbsp;{update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-eye">查看</i>', $url, [
                            'title' => Yii::t('app', '查看'),
                            'class' => 'del btn btn-primary btn-xs',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fa fa-edit">编辑</i>', $url, [
                            'title' => Yii::t('app', '编辑'),
                            'class' => 'del btn btn-success btn-xs',
                        ]);
                    },
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
            'before'=>$this->render('_search', ['model' => $searchModel]),
            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>刷新列表', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>true
        ],
    ]);
    Pjax::end();
    ?>

</div>
