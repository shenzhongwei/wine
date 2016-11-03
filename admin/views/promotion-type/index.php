<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\jui\AutoComplete;
use admin\models\PromotionType;
use admin\models\Dics;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\PromotionTypeSearch $searchModel
 */

$this->title = '活动分类';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promotion-type-index">
<?php
       echo GridView::widget([
           "options" => [
               // ...其他设置项
               "id" => "promotion-type"
           ],
           'dataProvider' => $dataProvider,
           'filterModel' => $searchModel,
           'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
           'headerRowOptions'=>['class'=>'kartik-sheet-style'],
           'filterRowOptions'=>['class'=>'kartik-sheet-style'],
           'pjax'=>true,  //pjax is set to always true for this demo
           'pjaxSettings'=>[
               'options'=>[
                   'id'=>'promotion_type_pjax',
               ],
               'neverTimeout'=>true,
           ],
           'columns' => [
               [
                   'class'=>'kartik\grid\CheckboxColumn',
                   'width'=>'4%',
                   'hAlign'=>'center',
                   'vAlign'=>'middle',
               ],
               [
                   'header'=>'分类名',
                   'width'=>'12%',
                   'attribute'=>'name',
                   'hAlign'=>'center',
                   'vAlign'=>'middle',
                   'filterType'=>AutoComplete::className(),
                   'filterWidgetOptions'=>[
                       'clientOptions' => [
                           'source' =>PromotionType::GetNames(),
                       ],
                   ]
               ],
               [
                   'header'=>'促销组别',
                   'width'=>'10%',
                   'attribute'=>'class',
                   'format'=>'html',
                   'hAlign'=>'center',
                   'vAlign'=>'middle',
                   'value'=>function($data){
                       return PromotionType::getPromotionClass($data->class);
                   },
                   'filterType'=>GridView::FILTER_SELECT2,
                   'filter'=>Dics::getPromotionClass(),
                   'filterWidgetOptions'=>[
                       'options'=>['placeholder'=>'请选择促销组别'],
                       'pluginOptions' => ['allowClear' => true],
                   ],
               ],
               [
                   'header'=>'促销环境',
                   'width'=>'12%',
                   'format'=>'html',
                   'attribute'=>'env',
                   'hAlign'=>'center',
                   'vAlign'=>'middle',
                   'value'=>function($data){
                       return PromotionType::getPromotionEnv($data->env);
                   },
                   'filterType'=>GridView::FILTER_SELECT2,
                   'filter'=>Dics::getPromotionEnv(),
                   'filterWidgetOptions'=>[
                       'options'=>['placeholder'=>'请选择促销环境'],
                       'pluginOptions' => ['allowClear' => true],
                   ],
               ],
               [
                   'header'=>'促销形式',
                   'width'=>'10%',
                   'attribute'=>'group',
                   'hAlign'=>'center',
                   'format'=>'html',
                   'vAlign'=>'middle',
                   'value'=>function($data){
                       return PromotionType::getPromotionGroup($data->group);
                   },
                   'filterType'=>GridView::FILTER_SELECT2,
                   'filter'=>Dics::getPromotionGroup(),
                   'filterWidgetOptions'=>[
                       'options'=>['placeholder'=>'请选择促销形式'],
                       'pluginOptions' => ['allowClear' => true],
                   ],
               ],
               [
                   'header'=>'促销限制',
                   'width'=>'10%',
                   'attribute'=>'limit',
                   'hAlign'=>'center',
                   'format'=>'html',
                   'vAlign'=>'middle',
                   'value'=>function($data){
                       return PromotionType::getPromotionLimit($data->limit);
                   },
                   'filterType'=>GridView::FILTER_SELECT2,
                   'filter'=>Dics::getPromotionLimit(),
                   'filterWidgetOptions'=>[
                       'options'=>['placeholder'=>'请选择限制类型'],
                       'pluginOptions' => ['allowClear' => true],
                   ],
               ],
               [
                   'label'=>'添加时间',
                   'width'=>'18%',
                   'attribute'=>'regist_at',
                   'hAlign'=>'center',
                   'vAlign'=>'middle',
                   'format'=>['date','php:Y年m月d日'],
                   'value'=>function($model){
                       return $model->regist_at;
                   },
                   'filterType'=>GridView::FILTER_DATE,
                   'filterWidgetOptions'=>[
                       // inline too, not bad
                       'language' => 'zh-CN',
                       'options' => ['placeholder' => '发布日期','readonly'=>true],
                       'pluginOptions' => [
                           'format' => 'yyyy年mm月dd日',
                           'autoclose' => true,

                       ]
                   ]
               ],
               [
                   'label'=>'上架状态',
                   'width'=>'12%',
                   'attribute'=>'is_active',
                   'hAlign'=>'center',
                   'vAlign'=>'middle',
                   'class'=>'kartik\grid\BooleanColumn',
                   'trueIcon'=>'<label class="label label-success">上架中</label>',
                   'falseIcon'=>'<label class="label label-danger">已下架</label>',
                   'trueLabel'=>'上架中',
                   'falseLabel'=>'已下架',
               ],
               [
                   'header'=>'操作',
                   'width'=>'12%',
                   'hAlign'=>'center',
                   'vAlign'=>'middle',
                   'class' => 'kartik\grid\ActionColumn',
                   'template'=>'{update}&nbsp;&nbsp;&nbsp;{delete}',
                   'buttons' => [
                       'view' => function ($url, $model) {
                           return '';
                       },
                       'update' => function ($url, $model) {
                           return Html::a(Yii::t('app','Update'), $url, [
                               'data-pjax'=>0,
                               'title' => Yii::t('app', '编辑'),
                               'class' => 'btn btn-primary btn-xs',
                           ]);
                       },
                       'delete' => function ($url, $model) {
                           if($model->is_active == 0){
                               return Html::a(Yii::t('app','Up'), $url, [
                                   'title' => Yii::t('app', '上架该类型'),
                                   'class' => 'btn btn-success btn-xs',
                                   'data-confirm' => '确认上架该类型，一旦上架该类型下的有效促销活动将可见？',
                               ]);
                           }else{
                               return Html::a(Yii::t('app','Down'), $url, [
                                   'title' => Yii::t('app', '下架该类型'),
                                   'class' => 'btn btn-danger btn-xs',
                                   'data-confirm' => '确认下架该类型，一旦下架该类型下的有效促销活动将不可见？',
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
                   Html::a("批量上架", "javascript:void(0);", ["class" => "btn btn-primary",'id'=>'type_up']).
                   Html::a("批量下架", "javascript:void(0);", ["class" => "btn btn-primary",'style'=>'margin-left:0.1%','id'=>'type_down']):'',
               'after'=>false,
               'showPanel'=>true,
               'showFooter'=>true
           ],
           'export'=>[
               'fontAwesome'=>true
           ],
    ]); ?>

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

        $("#type_up,#type_down").on("click", function () {
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
            var keys = $("#promotion-type").yiiGridView("getSelectedRows");
            if(keys == ''){
                layer.msg('请选择需要操作的类型',{
                    icon: 0,
                    time: 1500 //2秒关闭（如果不配置，默认是3秒）
                });
                return false;
            }
            var button = $(this).attr('id');
            var confirm = '';
            if(button == 'type_up'){
                confirm = '确认上架类别？一旦上架用户可参与有效的促销活动';
            }else if(button == 'type_down') {
                confirm = '确认下架类别？一旦下架用户将无法参与有效的促销活动';
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
                $.post(toRoute('promotion-type/patch'),{
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
                        $.pjax.reload({container:"#promotion_type_pjax"});
                    }else{
                        return false;
                    }
                },'json');
            });
        });
    }
</script>
