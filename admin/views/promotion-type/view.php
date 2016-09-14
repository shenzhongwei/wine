<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use admin\models\PromotionType;
/**
 * @var yii\web\View $this
 * @var admin\models\PromotionType $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '优惠券分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promotion-type-view">

    <?= DetailView::widget([
            'model' => $model,
            'condensed'=>false,
            'hover'=>true,
            'mode'=>Yii::$app->request->get('edit')=='t' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'panel'=>[
            'heading'=>$this->title,
            'type'=>DetailView::TYPE_INFO,
        ],
        'attributes' => [
            'id',
            [
                'attribute'=>'class',
                'format'=>'html',
                'value'=>PromotionType::getPromotionTypes($model),
            ],
            [
                'attribute'=>'group',
                'format'=>'html',
                'value'=>PromotionType::getPromotionGroup($model),
            ],
            'name',
            [
                'attribute'=>'regist_at',
                'value'=>date('Y-m-d H:i:s',$model->regist_at),
            ],
            [
                'attribute'=>'is_active',
                'format'=>'html',
                'value'=>empty($model->is_active)?'<p><span class="label label-danger"><i class="fa fa-times"></i>否</span></p>':'<p><span class="label label-primary"><i class="fa fa-check"></i>是</span></p>',
    ],
            [
                'label'=>'上架状态修改时间',
                'attribute'=> 'active_at',
                'value'=>date('Y-m-d H:i:s',$model->active_at),
            ],
        ],

    ]) ?>
    <p style="margin: 0 auto;text-align: center;">
        <?= Html::a('返回', 'javascript:history.go(-1);', ['class' => 'btn btn-primary']) ?>
    </p>
</div>
