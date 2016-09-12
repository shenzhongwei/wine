<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\UserInfo $model
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-info-view">
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
            'phone',
            'sex',
            'head_url:url',
            'birth',
            'nickname',
            'realname',
            'invite_user_id',
            'is_vip',
            'invite_code',
            [
                'attribute'=>'status',
                'value'=>empty($model->status)?'删除':'正常'
            ],
            'created_time'
        ],

    ]) ?>
    <p style="margin: 0 auto;text-align: center;">
        <?= Html::a('返回', 'javascript:history.go(-1);', ['class' => 'btn btn-primary']) ?>
    </p>
</div>
