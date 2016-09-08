<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use yii\helpers\Url;
/**
 * @var yii\web\View $this
 * @var admin\models\MerchantInfo $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Merchant Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<!--引用css-->
<?=Html::cssFile('@web/css/wine/pop.css')?>


<div class="merchant-info-view">
    <?= DetailView::widget([
            'model' => $model,
            'condensed'=>true,
            'hover'=>true,
            'mode'=>Yii::$app->request->get('edit')=='t' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'panel'=>[
            'heading'=>$this->title,
            'type'=>DetailView::TYPE_INFO,
        ],
        'attributes' => [
            'id',
            'name',
            [
                'attribute'=>'wa_id',
                'format'=>'html',
                'value'=>'<a>'.$model->wa_id.'</a>'
            ],
            'address',
            'lat',
            'lng',
            'phone',
            'registe_at',
            [
                'attribute'=>'is_active',
                'value'=>$model->is_active==0?'否':'是',

            ],
            [
                'attribute'=>'active_at',
                'value'=>empty($model->active_at)?'':date('Y-m-d H:i:s',$model->active_at),
            ],
        ],
        'deleteOptions'=>[
            'url'=>['delete', 'id' => $model->id],
            'data'=>[
                'confirm'=>Yii::t('app', 'Are you sure you want to delete this item?'),
                'method'=>'post',
            ],
        ],
        'enableEditMode'=>true,
    ]) ?>
    <p style="margin: 0 auto;text-align: center;">
        <?= Html::a('返回', 'index', ['class' => 'btn btn-primary']) ?>
    </p>
</div>
    <!--点击后台商户管理员id后 弹出的显示框-->
    <div class="pop_hide"></div>
    <div class="pop_showbrand">
        <!--关闭按钮-->
        <button class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>
        <h4 class="modal-title" style="padding: 5px;color: #1c94c4;font-size: 20px;font-family:华文楷体;">后台商户管理员信息</h4>
        <div style="border-radius: 5px ;border: 1px solid #CCCCCC;padding-top: 10px;text-align: center"></div>
    </div>
<?php
$tourl=\yii\helpers\Url::toRoute('/manager/view');
$imgpath=Yii::$app->params['img_path'];
$Js=<<<Js
    click_pop('{$imgpath}','{$tourl}');
Js;
$this->registerJs($Js);
?>
<script type="text/javascript" src="<?=\yii\helpers\Url::to('@web/js/wine/pop.js') ?>"></script>
