<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use admin\models\EmployeeInfoSearch;
/**
 * @var yii\web\View $this
 * @var admin\models\EmployeeInfo $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '配送人员列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;



//配送人员当前状态

?>
<div class="employee-info-view">

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
            'name',
            'phone',
            [
                'attribute'=>'type',
                'value'=>empty($model->type)?'商家':'门店',
            ],
            [
                'label'=>'所属商家/门店名称',
                'attribute'=>'owner_id',
                'format'=>'html',
                'value'=>admin\models\EmployeeInfoSearch::getOwnerName($model)
            ],
            [
                'attribute'=>'register_at',
                'label'=>'注册时间',
                'value'=>empty($model->register_at)?'':date('Y-m-d H:i:s',$model->register_at)
            ],

            [
                'attribute'=>'status',
                'format'=>'html',
                'value'=>EmployeeInfoSearch::getEmploySattus($model)
            ],
        ],

    ]) ?>
    <p style="margin: 0 auto;text-align: center;">
        <?= Html::a('返回', 'javascript:history.go(-1);', ['class' => 'btn btn-primary']) ?>
    </p>
</div>
