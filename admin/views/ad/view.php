<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\AdList $model
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '广告列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ad-list-view">

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
                'attribute'=>'type',
                'value'=>\admin\models\Dics::find()->where(['type'=>'图片类型','id'=>$model->type])->one()->name
            ],
            [
                'attribute'=> 'target_name',
                'value'=>\admin\models\AdList::getOneName($model)
            ],
            [
                'attribute'=>'pic',
                "format" => "raw",
                'value'=>Html::img('../../../photo'.$model->pic,[
                        'width'=>"300px",'height'=>"auto",
                    ]),

            ],
            'url:url',
            [
                'attribute'=>'is_show',
                'format'=>'html',
                'value'=>empty($model->is_show)?'不显示':'显示'
            ]
        ],
    ]) ?>
    <p style="margin: 0 auto;text-align: center;">
        <?= Html::a('返回', 'javascript:history.go(-1);', ['class' => 'btn btn-primary']) ?>
    </p>
</div>
