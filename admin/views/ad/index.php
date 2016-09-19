<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\AdListSearch $searchModel
 */

$this->title = '广告列表';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile("@web/js/good/_script.js");
?>

<div class="ad-list-index">
    <?php
    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'header'=>'序号',
                'options'=>['style'=>'width:50px'],
                'class' => 'yii\grid\SerialColumn'
            ],
            [
                'attribute'=>'type',
                'value'=>function($data){
                    $query=\admin\models\Dics::find()->where(['type'=>'图片类型','id'=>$data->type])->one();
                    return empty($query)?'':$query->name;
                },
            ],
            [
                'attribute'=> 'target_name',
                'value'=>function($data){
                   return \admin\models\AdList::getOneName($data);
                },
            ],
            [
                'attribute'=>'pic',
                "format" => "raw",
                'value'=>function($data){
                    return Html::img('../../../photo'.$data->pic,[
                        'width'=>"50px",'height'=>"50px","onclick"=>"ShowImg(this);",'style'=>'cursor:pointer','title'=>"点击放大"
                    ]);
                }
            ],
            'url:url',
            [
                'attribute'=>'is_show',
                'format'=>'html',
                'value'=>function($data){
                    return empty($data->is_show)?'<p><span class="label label-danger"><i class="fa fa-times"></i>不显示</span></p>':
                        '<p><span class="label label-primary"><i class="fa fa-check"></i>显示</span></p>';
                }
            ],
            [
                'header' => '操作',
                'class' => 'yii\grid\ActionColumn',
                'template'=> '{update}&nbsp;&nbsp;{delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fa fa-edit">编辑</i>', $url, [
                            'title' => Yii::t('app', '编辑'),
                            'class' => 'btn btn-primary btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        if($model->is_show == 0){
                            return Html::a('<i class="fa fa-star">显示</i>', $url, [
                                'title' => Yii::t('app', '显示该图片'),
                                'class' => 'btn btn-success btn-xs',
                                'data'=>['confirm'=>'确认显示该广告？一旦确定用户将会看到该广告？']
                            ]);
                        }else{
                            return Html::a('<i class="fa fa-star-o">不显示</i>', $url, [
                                'title' => Yii::t('app', '不显示该图片'),
                                'class' => 'btn btn-danger btn-xs',
                                'data'=>['confirm'=>'确认不显示该广告？一旦确定用户将无法看到该广告？']
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
            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>刷新列表', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]);
    Pjax::end();
    ?>

</div>
