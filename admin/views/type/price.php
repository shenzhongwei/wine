
<?php

use kartik\grid\GridView;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\GoodType $model
 */
?>
<?php echo
GridView::widget([
    'dataProvider' => $dataProvider,
    'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
    'headerRowOptions'=>['class'=>'kartik-sheet-style'],
    'floatHeader'=>false,
//        'pjax'=>true,
    'columns' => [
        [
            'class'=>'kartik\grid\SerialColumn',
            'contentOptions'=>['class'=>'kartik-sheet-style'],
            'width'=>'10%',
            'header'=>'',
            'headerOptions'=>['class'=>'kartik-sheet-style']
        ],
        [
            'attribute'=>'start',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'20%',
            'value'=>function($model){
                return $model->start;
            }
        ],
        [
            'attribute'=>'end',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'width'=>'20%',
            'value'=>function($model){
                return $model->end == '+∞' ? '无穷大':$model->end;
            }
        ],
        [
            'header'=>'类型',
            'attribute'=>'type',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'mergeHeader'=>true,
            'width'=>'20%',
            'value'=>function($model){
                return $model->type0->name;
            }
        ],
        [
            'header' => '操作',
            'width'=>'10%',
            'class' =>  'kartik\grid\ActionColumn',
            'buttons' => [
                'view' => function ($url, $model) {
                    return '';
                },
                'update' => function ($url, $model) {
                    return Html::a(Yii::t('app', 'Update'), '#', [
                        'data-toggle' => 'modal',    //弹框
                        'data-target' => '#price-modal',    //指定弹框的id
                        'data-id' => $model->id,
                        'title' => Yii::t('app', '更新'),
                        'class' => 'btn btn-primary btn-xs update',
                    ]);
                },
                'delete' => function ($url, $model) {
                    return Html::a(Yii::t('app', 'Delete'), ['price-delete', 'id' => $model->id], [
                        'title' => Yii::t('app', '删除该区间'),
                        'class' => 'btn btn-danger btn-xs',
                        'data-confirm' => '确定删除该区间，一旦删除则无法恢复？',
                    ]);
                }
            ],
        ],
    ],
    'responsive'=>false,
    'hover'=>true,
    'condensed'=>true,
    'bordered'=>true,
    'striped'=>false,
    'persistResize'=>false,
    'toolbar'=> [
        ['content'=>
            Html::a('<i class="glyphicon glyphicon-plus"></i>', '#',[
                'data-toggle' => 'modal',    //弹框
                'data-target' => '#price-modal',    //指定弹框的id
                'title'=>'新增区间',
                'class'=>'btn btn-success create'
            ]).
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['view','id'=>$model->id,'key'=>'price'], ['class'=>'btn btn-default', 'title'=>'刷新列表'])
        ],
        '{toggleData}',
        '{export}',
    ],
    'panel' => [
        'heading'=>false,
        'after'=>false,
        'footer'=>false,
    ],
    'export'=>[
        'fontAwesome'=>true
    ],
])
?>

<?php

\yii\bootstrap\Modal::begin([
    'id' => 'price-modal',
    'options' => [
        'tabindex' => false
    ],
]);
$updateUrl = \yii\helpers\Url::toRoute('price-update');  //当前控制器下的view方法
$createUrl = \yii\helpers\Url::toRoute(['price-create','type'=>$model->id]);  //当前控制器下的view方法
$Js = <<<JS
         $('.update').on('click', function () {  //查看详情的触发事件
          $('.good-price-create').remove();
          $('#price-modal').find('.modal-title').html('更新区间');
            $.get('{$updateUrl}', { id:$(this).closest('tr').data('key')  },
                function (data) {
                    $('#price-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
             );
         });
$('.create').on('click', function () {  //查看详情的触发事件
          $('.good-price-update').remove();
          $('#price-modal').find('.modal-title').html('新增区间');
            $.get('{$createUrl}', {},
                function (data) {
                    $('#price-modal').find('.modal-body').html(data);  //给该弹框下的body赋值
                }
             );
         });
JS;
$this->registerJs($Js);
\yii\bootstrap\Modal::end();
?>
<script>
    $("#price-modal").on("hidden.bs.modal", function(){
        $("#price-form")[0].reset();//重置表单
        $('#goodpricefield-end').val('+∞');
    });
    $("#update-modal").on("hidden.bs.modal", function(){
        $("#update-form")[0].reset();//重置表单
    });
</script>