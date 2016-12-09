<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use admin\models\UserAccount;
use yii\jui\AutoComplete;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\UserAccountSearch $searchModel
 */

$this->title = '账户管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-info-index">
    <?php
    echo GridView::widget([
        "options" => [
            // ...其他设置项
            "id" => "account_info"
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>true,  //pjax is set to always true for this demo
        'pjaxSettings'=>[
            'options'=>[
                'id'=>'account_pjax',
            ],
            'neverTimeout'=>true,
        ],
        'columns' => [
            [
                'header'=>'',
                'width'=>'5%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'contentOptions'=>['class'=>'kartik-sheet-style'],
                'class' => 'kartik\grid\SerialColumn'
            ],
            [
                'header'=>'账户等级',
                'width'=>'14%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=>'level',
                'format'=>'raw',
                'value'=> function($model){
                    return $model->level == 1 ? '管理员账户':($model->level == 2 ? '用户账户':'<span class="no-set">未设置</span>');
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>UserAccount::GetLevels(),
                'filterWidgetOptions'=>[
                    'options'=>['placeholder'=>'账户等级'],
                    'pluginOptions' => ['allowClear' => true],
                    'hideSearch' => true,
                ]
            ],
            [
                'header'=>'账户类型',
                'attribute'=>'type',
                'width'=>'14%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'format' => 'html',
                'value'=> function($model){
                    $typeArr = [1=>'余额账户',2=>'支付宝账户',3=>'微信账户'];
                    return empty($typeArr[$model->type]) ? '<span class="no-set">未设置</span>':$typeArr[$model->type];
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filterWidgetOptions'=>[
                    'data'=>UserAccount::GetTypes(),
                    'options'=>['placeholder'=>'账户类型'],
                    'pluginOptions' => ['allowClear' => true],
                    'hideSearch' => true,
                ],
            ],
            [
                'header'=>'账户所属',
                'hAlign'=>'center',
                'width'=>'12%',
                'vAlign'=>'middle',
                'attribute'=>'target',
                'format'=>'raw',
                'value'=> function($model){
                    return UserAccount::getAccountAcceptName($model);
                },
                'filterType'=>AutoComplete::className(),
                'filterWidgetOptions'=>[
                    'clientOptions' => [
                        'source' =>UserAccount::getTargets(),
                    ],
                ],
            ],
            [
                'attribute'=>'end',
                'label' => '余额',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'value'=> function($model){
                    return '¥'.$model->end;
                },
                'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
            ],
            [
                'label'=>'支付密码',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'class'=>'kartik\grid\BooleanColumn',
                'trueIcon'=>'<label class="label label-success">已设置</label>',
                'falseIcon'=>'<label class="label label-danger">未设置</label>',
                'attribute' => 'set_pwd',
                'trueLabel'=>'已设置',
                'falseLabel'=>'未设置',
            ],
            [
                'label'=>'创建时间',
                'width'=>'14%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=>'create_at',
                'format'=>['date','php:Y年m月d日'],
                'value'=>function($model){
                    return $model->create_at;
                },
                'filterType'=>GridView::FILTER_DATE,
                'filterWidgetOptions'=>[
                    // inline too, not bad
                    'language' => 'zh-CN',
                    'options' => ['placeholder' => '创建时间','readonly'=>true],
                    'pluginOptions' => [
                        'format' => 'yyyy年mm月dd日',
                        'autoclose' => true,

                    ]
                ]
            ],

            [
                'label'=>'状态',
                'width'=>'10%',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'class'=>'kartik\grid\BooleanColumn',
                'trueIcon'=>'<label class="label label-success">使用中</label>',
                'falseIcon'=>'<label class="label label-danger">已冻结</label>',
                'attribute' => 'is_active',
                'trueLabel'=>'使用中',
                'falseLabel'=>'已冻结',
            ],
            [
                'header' => '操作',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'11%',
                'template'=>'{view}',
                'class' =>  'kartik\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-eye"> 查看明细</i>', $url, [
                            'data-pjax'=>0,
                            'title' => '查看详细信息',
                            'class' => 'btn btn-info btn-xs',
                        ]);
                    },
                ],
            ],
        ],
        // set your toolbar
        'toolbar'=> [
            ['content'=>
                Html::a('<i class="glyphicon glyphicon-repeat">刷新列表</i>', ['index'], [ 'class'=>'btn btn-default', 'title'=>'刷新列表'])
            ],
            '{toggleData}',
            '{export}',
        ],
        'responsive'=>false,
        'condensed'=>true,
        'panel' => [
            'type'=>'info',
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'before'=>'',
            'after'=>false,
            'showPanel'=>true,
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]); ?>

</div>
<script language="JavaScript">
    $(function () {
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
        $('.ui-autocomplete').css('z-index','99999');
        $('.datepicker-days').css('z-index','99999');
    })
</script>