<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
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

    <p>
        <?php /* echo Html::a('Create Good Rush', ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
      //  'pjax'=>true, // pjax is set to always true for this demo
        'columns' => [
            [
                'class'=>'kartik\grid\SerialColumn',
                'contentOptions'=>['class'=>'kartik-sheet-style'],
                'width'=>'36px',
                'header'=>'',
                'headerOptions'=>['class'=>'kartik-sheet-style']
            ],
            [
                'attribute'=>'good_name',
                'header'=>'商品名称',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'210px',
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
                'width'=>'8%',
                'value'=>function($model) {
                    return '¥'.$model->price.'/'.$model->g->unit;
                },
                'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
            ],
            [
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'attribute'=>'limit',
                'width'=>'8%',
                'value'=>function($model) {
                    return $model->limit.$model->g->unit;
                },
                'filterInputOptions'=>['onkeyup'=>'this.value=this.value.replace(/\D/gi,"")','class'=>'form-control'],
            ],
            [
                'attribute'=>'start_at',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'9%',
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
                'filterInputOptions'=>['readonly'=>true,'style'=>['width'=>'200px']],
            ],
            [
                'attribute'=>'end_at',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'width'=>'9%',
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
                'filterInputOptions'=>['readonly'=>true,'style'=>['width'=>'200px']],
            ],
            [
                'label'=>'抢购状态',
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'is_active',
                'vAlign'=>GridView::ALIGN_LEFT,
                'width'=>'108px',
                'trueLabel'=>'上架中',
                'falseLabel'=>'已下架',
                'trueIcon'=>'<label class="label label-info">上架中</label>',
                'falseIcon'=>'<label class="label label-danger">已下架</label>',
            ],

            [
                'label'=>'原价',
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'mergeHeader'=>true,
                'headerOptions'=>['class'=>'kartik-sheet-style'],
                'attribute'=>'g.price',
                'width'=>'8%',
                'value'=>function($model) {
                    return '¥'.$model->g->price.'/'.$model->g->unit;
                }
            ],
            [
                'label'=>'商品状态',
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'g.is_active',
                'vAlign'=>GridView::ALIGN_LEFT,
                'width'=>'108px',
                'mergeHeader'=>true,
                'headerOptions'=>['class'=>'kartik-sheet-style'],
                'trueLabel'=>'上架中',
                'falseLabel'=>'已下架',
            ],
            [
                'header' => '操作',
                'class' => 'kartik\grid\ActionColumn',
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
                                'title' => Yii::t('app', '上架该商品'),
                                'class' => 'btn btn-success btn-xs',
                                'data-confirm'=>'确定上架该抢购商品？',
                            ]);
                        }else{
                            return Html::a(Yii::t('app','Down'), $url, [
                                'title' => Yii::t('app', '下架该商品'),
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
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],['type'=>'button', 'title'=>'发布抢购', 'class'=>'btn btn-primary']).
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>'刷新列表'])
            ],
            '{toggleData}',
            '{export}',
        ],
        'responsive'=>false,
        'hover'=>true,
        'condensed'=>true,
        'bordered'=>true,
        'striped'=>false,
        'floatHeader'=>true,
        'persistResize'=>false,
        'panel' => [
            'type'=>'info',
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'showPanel'=>true,
            'showFooter'=>true
        ],
        'export'=>[
            'fontAwesome'=>true
        ],
    ]); Pjax::end(); ?>

</div>

<script language="JavaScript">
    $(function (){
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
        $('.ui-autocomplete').css('z-index','99999');
    });
</script>
