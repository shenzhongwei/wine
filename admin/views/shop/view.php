<?php

use yii\helpers\Html;
use \kartik\detail\DetailView;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var admin\models\ShopInfo $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '信息列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\admin\assets\AppAsset::register($this);
// here
$this->registerJsFile("@web/js/good/_script.js");
?>

<div class="">
    <div class="ibox-content">
        <div class="good-info-view">
            <h1><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span> <?= Html::encode($this->title) ?></h1>
            <p>
                <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
                <?= Html::a(Yii::t('app', $model->is_active == 1 ? 'Lock':'Unlock'), ['delete', 'id' => $model->id], [
                    'class' =>  $model->is_active == 1 ? 'btn btn-sm btn-danger':'btn btn-sm btn-info',
                    'data-confirm' => Yii::t('app', $model->is_active == 1 ? '确定冻结该店铺，一旦冻结，用户将无法看到该店铺的商品':'确定解除冻结，一旦接触，用户将可以购买该店铺的商品'),
                    'data-method' => 'post',
                ]); ?>
                <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-sm btn-success']) ?>
            </p>

            <div class="row">
                <div class="col-sm-4">
                    <?= DetailView::widget([
                        'model'=>$model,
                        'condensed'=>true,
                        'striped'=>false,
                        'mode'=>DetailView::MODE_VIEW,
                        'attributes' => [

                            [
                                'attribute'=>'name',
                                'value'=> $model->name,
                            ],
                            [
                                'label'=>'归属商户',
                                'attribute'=>'merchant',
                                'format' => 'raw',
                                'value'=> Html::a($model->merchant0->name,['merchant/view', 'id' => $model->merchant0->id], ['title' => '查看商户信息','class'=>'btn btn-link btn-xs']),
                            ],
                            [
                                'label'=>'后台账号',
                                'attribute'=>'wa_id',
                                'format' => 'raw',
                                'value'=> Html::a($model->wa->wa_name,['manager/update', 'id' => $model->wa->wa_id], ['title' => '查看后台登录信息','class'=>'btn btn-link btn-xs']),
                            ],
                            'contacter',
                            'phone',
                            [
                                'attribute'=>'limit',
                                'value'=> $model->limit.'米'
                            ],
                            [
                                'label'=>'最低订单金额',
                                'attribute'=>'least_money',
                                'value'=> '¥'.$model->least_money
                            ],
                            [
                                'label'=>'配送费',
                                'attribute'=>'send_bill',
                                'value'=> '¥'.$model->send_bill
                            ],
                            [
                                'label'=>'免配送金额',
                                'attribute'=>'no_send_need',
                                'value'=> '¥'.$model->no_send_need
                            ],
                            [
                                'label'=>'状态',
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => $model->is_active==0 ? '<label class="label label-danger">冻结中</label>':'<label class="label label-success">已激活</label>'

                            ],
                            [
                                'label'=>$model->is_active == 0 ? '冻结时间':'激活时间',
                                'attribute'=>'active_at',
                                'format'=>["date", "php:Y年m月d日"],
                                'value'=>$model->active_at,
                            ],
                            [
                                'label'=>'入驻时间',
                                'attribute'=>'regist_at',
                                'format'=>["date", "php:Y年m月d日"],
                                'value'=> $model->regist_at
                            ],
                        ],
                        'hAlign' =>DetailView::ALIGN_MIDDLE,
                        'vAlign' =>DetailView::ALIGN_CENTER,
                    ]) ?>
                </div>
                <div class="col-sm-4">
                    <?= DetailView::widget([
                        'model'=>$model,
                        'condensed'=>true,
                        'striped'=>false,
                        'mode'=>DetailView::MODE_VIEW,
                        'attributes' => [

                            [
                                'attribute'=>'name',
                                'value'=> $model->name,
                            ],
                            [
                                'label'=>'归属商户',
                                'attribute'=>'merchant',
                                'format' => 'raw',
                                'value'=> Html::a($model->merchant0->name,['merchant/view', 'id' => $model->merchant0->id], ['title' => '查看商户信息','class'=>'btn btn-link btn-xs']),
                            ],
                            [
                                'label'=>'后台账号',
                                'attribute'=>'wa_id',
                                'format' => 'raw',
                                'value'=> Html::a($model->wa->wa_name,['manager/update', 'id' => $model->wa->wa_id], ['title' => '查看后台登录信息','class'=>'btn btn-link btn-xs']),
                            ],
                            'contacter',
                            'phone',
                            [
                                'attribute'=>'limit',
                                'value'=> $model->limit.'米'
                            ],
                            [
                                'label'=>'最低订单金额',
                                'attribute'=>'least_money',
                                'value'=> '¥'.$model->least_money
                            ],
                            [
                                'label'=>'配送费',
                                'attribute'=>'send_bill',
                                'value'=> '¥'.$model->send_bill
                            ],
                            [
                                'label'=>'免配送金额',
                                'attribute'=>'no_send_need',
                                'value'=> '¥'.$model->no_send_need
                            ],
                            [
                                'label'=>'状态',
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => $model->is_active==0 ? '<label class="label label-danger">冻结中</label>':'<label class="label label-success">已激活</label>'

                            ],
                            [
                                'label'=>$model->is_active == 0 ? '冻结时间':'激活时间',
                                'attribute'=>'active_at',
                                'format'=>["date", "php:Y年m月d日"],
                                'value'=>$model->active_at,
                            ],
                            [
                                'label'=>'入驻时间',
                                'attribute'=>'regist_at',
                                'format'=>["date", "php:Y年m月d日"],
                                'value'=> $model->regist_at
                            ],
                        ],
                        'hAlign' =>DetailView::ALIGN_MIDDLE,
                        'vAlign' =>DetailView::ALIGN_CENTER,
                    ]) ?>
                </div>
                <div class="col-sm-4">
                    <?= DetailView::widget([
                        'model'=>$model,
                        'condensed'=>true,
                        'striped'=>false,
                        'mode'=>DetailView::MODE_VIEW,
                        'attributes' => [

                            [
                                'attribute'=>'name',
                                'value'=> $model->name,
                            ],
                            [
                                'label'=>'归属商户',
                                'attribute'=>'merchant',
                                'format' => 'raw',
                                'value'=> Html::a($model->merchant0->name,['merchant/view', 'id' => $model->merchant0->id], ['title' => '查看商户信息','class'=>'btn btn-link btn-xs']),
                            ],
                            [
                                'label'=>'后台账号',
                                'attribute'=>'wa_id',
                                'format' => 'raw',
                                'value'=> Html::a($model->wa->wa_name,['manager/update', 'id' => $model->wa->wa_id], ['title' => '查看后台登录信息','class'=>'btn btn-link btn-xs']),
                            ],
                            'contacter',
                            'phone',
                            [
                                'attribute'=>'limit',
                                'value'=> $model->limit.'米'
                            ],
                            [
                                'label'=>'最低订单金额',
                                'attribute'=>'least_money',
                                'value'=> '¥'.$model->least_money
                            ],
                            [
                                'label'=>'配送费',
                                'attribute'=>'send_bill',
                                'value'=> '¥'.$model->send_bill
                            ],
                            [
                                'label'=>'免配送金额',
                                'attribute'=>'no_send_need',
                                'value'=> '¥'.$model->no_send_need
                            ],
                            [
                                'label'=>'状态',
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => $model->is_active==0 ? '<label class="label label-danger">冻结中</label>':'<label class="label label-success">已激活</label>'

                            ],
                            [
                                'label'=>$model->is_active == 0 ? '冻结时间':'激活时间',
                                'attribute'=>'active_at',
                                'format'=>["date", "php:Y年m月d日"],
                                'value'=>$model->active_at,
                            ],
                            [
                                'label'=>'入驻时间',
                                'attribute'=>'regist_at',
                                'format'=>["date", "php:Y年m月d日"],
                                'value'=> $model->regist_at
                            ],
                        ],
                        'hAlign' =>DetailView::ALIGN_MIDDLE,
                        'vAlign' =>DetailView::ALIGN_CENTER,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
