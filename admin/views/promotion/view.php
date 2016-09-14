<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use admin\models\PromotionType;
use admin\models\PromotionInfo;
/**
 * @var yii\web\View $this
 * @var admin\models\PromotionInfo $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '活动优惠列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promotion-info-view">

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
                'attribute'=>'pt_id',
                'value'=>PromotionType::getPromotionTypeName($model->pt_id),

            ],
            [
                'attribute'=>'limit',
                'value'=>PromotionType::getPromotionRangeById($model->limit),
            ],
            [
                'attribute'=>'target_id',
                'value'=>PromotionInfo::getNameByRange($model),
            ],
            'name',
            'condition',
            'discount',
            [
                'attribute'=> 'valid_circle',
                'value'=>empty($model->valid_circle)?'永久有效':$model->valid_circle.'天',
            ],

            [
                'attribute'=>'start_at',
                'value'=>empty($model->start_at)?'':date('Y-m-d H:i:s',$model->start_at),
            ],
            [
                'attribute'=>'end_at',
                'value'=>empty($model->end_at)?'':date('Y-m-d H:i:s',$model->end_at),
            ],
            [
                'attribute'=> 'time',
                'value'=>empty($model->time)?'无数次':$model->time.'次',
            ],

            [
                'attribute'=>'regist_at',
                'value'=>empty($model->regist_at)?'':date('Y-m-d H:i:s',$model->regist_at),
            ],
            [
                'attribute'=>'is_active',
                'format'=>'html',
                'value'=> empty($model->is_active)?'<p><span class="label label-danger"><i class="fa fa-times"></i>否</span></p>':
                        '<p><span class="label label-primary"><i class="fa fa-check"></i>是</span></p>',

            ],
            [
                'attribute'=>'active_at',
                'value'=>empty($model->active_at)?'':date('Y-m-d H:i:s',$model->active_at),
            ],
        ],
    ]) ?>
    <p style="margin: 0 auto;text-align: center;">
        <?= Html::a('返回', 'javascript:history.go(-1);', ['class' => 'btn btn-primary']) ?>
    </p>
</div>
