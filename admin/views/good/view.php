<?php

use yii\helpers\Html;
use \kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\GoodInfo $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '信息列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="wrapper wrapper-content">
    <div class="ibox-content">
        <div class="good-info-view">
            <h1><?= Html::encode($this->title) ?></h1>
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
                <div class="col-sm-11">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'merchant',
                            'type',
                            'brand',
                            'smell',
                            'color',
                            'dry',
                            'boot',
                            'breed',
                            'country',
                            'style',
                            'name',
                            'volum',
                            'price',
                            'unit',
                            'pic',
                            'number',
                            'detail:ntext',
                            'order',
                            'regist_at',
                            'is_active',
                            'active_at',
                        ],
                        'hAlign' =>DetailView::ALIGN_LEFT,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
