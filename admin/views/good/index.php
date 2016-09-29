<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use admin\models\GoodInfo;
use yii\jui\AutoComplete;
use admin\models\MerchantInfo;
use admin\models\GoodType;
use admin\models\GoodBrand;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\GoodSearch $searchModel
 */

$this->title = '商品列表';
$this->params['breadcrumbs'][] = $this->title;
\admin\assets\AppAsset::register($this);
// here
$this->registerJsFile("@web/js/good/_script.js");
?>
<div class="good-info-index">
    <?php Pjax::begin(['id'=>'goodinfos','timeout'=>5000]);
        echo GridView::widget([
        'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
            'headerRowOptions'=>['class'=>'kartik-sheet-style'],
            'filterRowOptions'=>['class'=>'kartik-sheet-style'],
            //   'pjax'=>true,  pjax is set to always true for this demo
            'columns' => [
                [
                    'class'=>'kartik\grid\SerialColumn',
                    'contentOptions'=>['class'=>'kartik-sheet-style'],
                    'width'=>'36px',
                    'header'=>'',
                    'headerOptions'=>['class'=>'kartik-sheet-style']
                ],
                [
                    'header'=>'商品名称',
                    'attribute'=>'name',
                    'format' => 'html',
                    'width'=>'12%',
                    'value'=> function($model){
                        return Html::a($model->name.$model->volum,['good/view', 'id' => $model->id],
                            ['title' => '查看商品详细','class'=>'btn btn-link btn-sm']
                        );
                    },
                    'filterType'=>AutoComplete::className(),
                    'filterWidgetOptions'=>[
                        'clientOptions' => [
                            'source' =>GoodInfo::GetGoodNames(),
                        ],
                    ]
                ],
                [
                    'header'=>'归属商户',
                    'attribute'=>'merchant',
                    'width'=>'8%',
                    'format' => 'html',
                    'value'=> function($model){
                        return Html::a($model->merchant0->name,['merchant/view', 'id' => $model->merchant0->id],
                            ['title' => '查看商品详细','class'=>'btn btn-link btn-sm']
                        );
                    },
                    'headerOptions'=>['width'=>120],
                    'filterType'=>GridView::FILTER_SELECT2,
                    'filterWidgetOptions'=>[
                        'data'=>MerchantInfo::GetMerchants(),
                        'options'=>['placeholder'=>'请选择商户'],
                        'pluginOptions' => ['allowClear' => true],
                    ],
                ],
                [
                    'header'=>'类型',
                    'attribute'=>'type',
                    'width'=>'8%',
                    'value'=> function($model){
                        return empty($model->type0) ? '无':$model->type0->name;
                    },
                    'filterType'=>GridView::FILTER_SELECT2,
                    'filterWidgetOptions'=>[
                        'data'=>GoodType::GetTypes(),
                        'options'=>['placeholder'=>'请选择类型'],
                        'pluginOptions' => ['allowClear' => true],
                    ],
                ],
                [
                    'header'=>'品牌',
                    'attribute'=>'brand',
                    'width'=>'8%',
                    'value'=> function($model){
                        return $model->brand0->name;
                    },
                    'filterType'=>GridView::FILTER_SELECT2,
                    'filterWidgetOptions'=>[
                        'data'=>GoodBrand::GetBrands(),
                        'options'=>['placeholder'=>'请选择品牌'],
                        'pluginOptions' => ['allowClear' => true],
                    ],
                ],
                [
                    'attribute'=>'price',
                    'label' => '单价',
                    'width'=>'8%',
                    'value'=> function($model){
                        return '¥'.$model->price.'/'.$model->unit;
                    },
                    'filterInputOptions'=>['onkeyup'=>'clearNoNum(this)','class'=>'form-control'],
                ],

                [
                    'header'=>'编号',
                    'attribute'=>'number',
                    'width'=>'9%',
                    'filterType'=>AutoComplete::className(),
                    'filterWidgetOptions'=>[
                        'clientOptions' => [
                            'source' =>GoodInfo::GetGoodNumbers(),
                        ],
                    ]
                ],
                [
                    'label'=>'发布时间',
                    'attribute'=>'regist_at',
                    'format'=>['date','php:Y年m月d日'],
                    'value'=>function($model){
                        return $model->regist_at;
                    },
                    'filterType'=>GridView::FILTER_DATE,
                    'filterWidgetOptions'=>[
                        // inline too, not bad
                        'language' => 'zh-CN',
                        'options' => ['placeholder' => '选择发布日期','readonly'=>true],
                        'pluginOptions' => [
                            'format' => 'yyyy年mm月dd日',
                            'autoclose' => true,

                        ]
                    ]
                ],
                [
                    'label'=>'状态',
                    'class'=>'kartik\grid\BooleanColumn',
                    'width'=>'8%',
                    'attribute' => 'is_active',
                    'vAlign'=>GridView::ALIGN_LEFT,
                    'trueLabel'=>'上架中',
                    'falseLabel'=>'已下架',
                    'trueIcon'=>'<label class="label label-info">上架中</label>',
                    'falseIcon'=>'<label class="label label-danger">已下架</label>',
                ],

                [

                    'header'=>'图片',
                    'width'=>'5%',
                    'headerOptions'=>['class'=>'kartik-sheet-style'],
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'mergeHeader'=>true,
                    'attribute'=>'pic',
                    "format" => "raw",
                    'value'=>function($model){
                        return empty($model->pic) ? '<label class="label label-primary">暂无</label>':Html::img('../../../photo'.$model->pic,[
                            'width'=>"50px",'height'=>"50px","onclick"=>"ShowImg(this);",'style'=>'cursor:pointer','title'=>"点击放大"
                        ]);
                    }
                ],
                [
                    'header'=>'详情',
                    'class' =>  'kartik\grid\ActionColumn',
                    'width'=>'5%',
                    'headerOptions'=>['class'=>'kartik-sheet-style'],
                    'buttons'=>[
                        'view' => function ($url, $model) {
                            return Html::a('点击查看', '#', [
                                'id'=>'detail',//属性
                                'data-toggle' => 'modal',    //弹框
                                'data-target' => '#good-modal',    //指定弹框的id
                                'class' => 'detail btn btn-link btn-xs',
                                'data-id' => $model->id,
                            ]);
                        },
                        'update' =>  function ($url, $model) {
                            return '';
                        },
                        'delete' =>function ($url, $model) {
                            return '';
                        },
                    ],
                ],

                [
                    'header' => '操作',
                    'class' =>  'kartik\grid\ActionColumn',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<i class="fa fa-eye">查看</i>', $url, [
                                'title' => Yii::t('app', Yii::t('app','View')),
                                'class' => 'btn btn-info btn-xs',
                            ]);
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
                                    'data-confirm' => Yii::t('app', 'GoodUpSure'),
                                ]);
                            }else{
                                return Html::a(Yii::t('app','Down'), $url, [
                                    'title' => Yii::t('app', '下架该商品'),
                                    'class' => 'btn btn-danger btn-xs',
                                    'data-confirm' => Yii::t('app', 'GoodDownSure'),
                                ]);
                            }
                        }
                    ],
                ],
            ],
            // set your toolbar
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],['type'=>'button', 'title'=>'发布商品', 'class'=>'btn btn-primary']).
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
<!--查看看详情弹出框  start-->
<?php

\yii\bootstrap\Modal::begin([
    'id' => 'good-modal',
    'header' => '<h4 class="modal-title">查看详情</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
]);
$requestUrl = \yii\helpers\Url::toRoute('detail');  //当前控制器下的view方法
$Js = <<<JS
         $('.detail').on('click', function () {  //查看详情的触发事件
            $.get('{$requestUrl}', { id:$(this).closest('tr').data('key')  },
                function (data) {
                    $('#good-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
             );
         });
JS;
$this->registerJs($Js);
\yii\bootstrap\Modal::end();
?>
<!--查看看详情弹出框  end-->
<script language="JavaScript">
    $(function (){
        $('.panel').find('.dropdown-toggle').unbind();
        $('.panel').find('.dropdown-toggle').attr('class','btn btn-default dropdown-toggle');
        $('.ui-autocomplete').css('z-index','99999');
        $('.datepicker-days').css('z-index','99999');
    });
</script>


