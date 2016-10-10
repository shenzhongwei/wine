<?php

use yii\helpers\Html;
use \kartik\detail\DetailView;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodInfo $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '信息列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\admin\assets\AppAsset::register($this);
// here
$this->registerJsFile("@web/js/good/_script.js");
?>

<div class="wrapper wrapper-content">
    <div class="ibox-content">
        <div class="good-info-view">
            <h1><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span> <?= Html::encode($this->title) ?></h1>
            <p>
                <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
                <?= Html::a(Yii::t('app', $model->is_active == 1 ? 'Down':'Up'), ['delete', 'id' => $model->id], [
                    'class' =>  $model->is_active == 1 ? 'btn btn-sm btn-danger':'btn btn-sm btn-info',
                    'data-confirm' => Yii::t('app', $model->is_active == 1 ? 'GoodDownSure':'GoodUpSure'),
                    'data-method' => 'post',
                ]); ?>
                <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-sm btn-success']) ?>
            </p>

            <div class="row">
                <div class="col-sm-5">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [

                            [
                                'attribute'=>'name',
                                'value'=> $model->name.$model->volum,
                            ],
                            [
                                'label'=>'归属商户',
                                'attribute'=>'merchant',
                                'format' => 'raw',
                                'value'=> Html::a($model->merchant0->name,['merchant/view', 'id' => $model->merchant0->id], ['title' => '查看商户信息','style'=>'color:#2a62bc;font-size:15px']),
                            ],
                            [
                                'attribute'=>'type',
                                'value'=> $model->type0->name,
                            ],
                            [
                                'attribute'=>'brand',
                                'value'=> empty($model->brand0) ? null:$model->brand0->name,
                            ],
                            [
                                'attribute'=>'smell',
                                'value'=> empty($model->smell0) ? null:$model->smell0->name,
                            ],
                            [
                                'attribute'=>'color',
                                'value'=> empty($model->color0) ? null:$model->color0->name,
                            ],
                            [
                                'attribute'=>'dry',
                                'value'=> empty($model->dry0) ? null:$model->dry0->name,
                            ],
                            [
                                'attribute'=>'boot',
                                'value'=> empty($model->boot0) ? null:$model->boot0->name,
                            ],
                            [
                                'attribute'=>'breed',
                                'value'=> empty($model->breed0) ? null:$model->breed0->name,
                            ],
                            [
                                'attribute'=>'country',
                                'value'=> empty($model->country0) ? null:$model->country0->name,
                            ],
                            [
                                'attribute'=>'style',
                                'value'=> empty($model->style0) ? null:$model->style0->name,
                            ],
                            [
                                'label'=>'原价',
                                'attribute'=>'price',
                                'value'=> $model->price.'/'.$model->unit,
                            ],
                            [
                                'label'=>'优惠价',
                                'attribute'=>'pro_price',
                                'value'=> $model->pro_price.'/'.$model->unit,
                            ],
                            'number',
                            [

                                'label'=>'图片',
                                'attribute'=>'pic',
                                "format" => "raw",
                                'value'=>Html::img('../../../photo'.$model->pic,[
                                    'height'=>"180px","onclick"=>"ShowImg(this);",'style'=>'cursor:pointer','title'=>"点击放大"
                                ]),
                            ],
                            [
                                'attribute'=>'detail',
                                "format" => "html",
                                'value'=>'<a class="btn btn-link btn-xs" href="'.Url::toRoute(['good/detail','id'=>$model->id]).'">点击查看</a>',
                            ],
                            [
                                'label'=>'发布时间',
                                'attribute'=>'regist_at',
                                'format'=>["date", "php:Y年m月d日"],
                                'value'=>$model->regist_at,
                            ],
                            [
                                'label'=>'状态',
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => $state =  $model->is_active==0 ? '<label class="label label-danger">已下架</label>':'<label class="label label-info">上架中</label>'

                            ],
                            [
                                'label'=>$model->is_active == 0 ? '下架时间':'上架时间',
                                'attribute'=>'active_at',
                                'format'=>["date", "php:Y-m-d H:i:s"],
                                'value'=>$model->regist_at,
                            ],
                        ],
                        'hAlign' =>DetailView::ALIGN_MIDDLE,
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute'=>'detail',
                                "format" => "html",
                                'value'=>$model->detail,
//                                'value'=>'<a class="btn btn-link btn-xs" href="'.Url::toRoute(['good/detail','id'=>$model->id]).'">点击查看</a>',
                            ],
                        ],
                        'hAlign' =>DetailView::ALIGN_MIDDLE,
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
