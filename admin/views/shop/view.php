<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\ShopInfo $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Shop Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$img_path=Yii::$app->params['img_path'];
?>
<!--引用css-->
<?=Html::cssFile('@web/css/wine/pop.css')?>
<div class="shop-info-view">
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
            [
                'attribute'=>'merchant',
                'value'=>\admin\models\MerchantInfoSearch::getOneMerchant($model->merchant)
            ],
            [
                'attribute'=>'address',
                'value'=>empty($model->province)?'':($model->province.'-'.$model->city.'-'.$model->district.' '.$model->region.$model->address)
            ],
            'lat',
            'lng',
            'limit',
            'least_money',
            'send_bill',
            [
                'attribute'=>'no_send_need',
                'value'=>'满'.$model->no_send_need.'元，免配送费'
            ],
            [
                'attribute'=>'bus_pic',
                'format'=>[
                    'image',
                    [
                        'width'=>'150',
                        'height'=>'150'
                    ]
                ],
                'value'=>$img_path.$model->bus_pic
            ],
            [
                'attribute'=>'logo',
                'format'=>[
                    'image',
                    [
                        'width'=>'50',
                        'height'=>'50'
                    ]
                ],
                'value'=>$img_path.$model->logo
            ],
            [
                'attribute'=>'is_active',
                'value'=>$model->is_active==0?'否':'是',

            ],
            [
                'attribute'=>'active_at',
                'value'=>empty($model->active_at)?'':date('Y-m-d H:i:s',$model->active_at),
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
    <h4 class="modal-title" style="padding: 5px;color: #1c94c4;font-size: 20px;font-family:华文楷体;">后台门店管理员信息</h4>
    <div style="border-radius: 5px ;border: 1px solid #CCCCCC;padding-top: 10px;text-align: center"></div>
</div>
<?php
$tourl=\yii\helpers\Url::toRoute('/manager/view');
$Js=<<<Js
    click_pop('{$img_path}','{$tourl}');
Js;
    $this->registerJs($Js);
?>
<script type="text/javascript" src="<?=\yii\helpers\Url::to('@web/js/wine/pop.js') ?>"></script>