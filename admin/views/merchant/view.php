<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var admin\models\MerchantInfo $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Merchant Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    /*弹出层*/
    .pop_hide{
        display: none;
        position: absolute;
        top: 0%;  left: 0%;
        width: 100%;  height: 100%;
        background-color: black;
        z-index:1001;
        -moz-opacity: 0.7;
        opacity:.70;
        filter: alpha(opacity=70);
    }
    .pop_showbrand{
        display: none;
        z-index:1002;  overflow: auto;
        position: absolute;top: 15%;
        left:0; right:0; margin-left: 0;margin-right: 0;
        margin: 10px auto;
        width:500px;
        height:auto;background-color: #ffffff;
        border:1px solid #CCCCCC;
        box-shadow:0 0 3px #000;
    }
</style>
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
                'value'=>empty($model->active_at)?'':$model->active_at,
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

    $('table a').click(function(){
        var html='';
        $.ajax({
            url:'{$tourl}',
            data:{'wa_id':$(this).text()},
            type:'post',
            dataType:'json',
            success:function(msg){
                    if(msg.state=='200'){
                        html='<table style="width:90%;margin:0 auto;">' +
                                 '<tr align="left"><th>后台登陆名</th><td>'+msg.data['username']+'</td></tr>' +
                                 '<tr align="left"><th>登陆密码</th><td>******</td></tr>' +
                                 '<tr align="left"><th>头像</th><td><img src="{$imgpath}'+msg.data['wa_logo']+'" width="50" height="50"></td></tr>' +
                                 '<tr align="left"><th>用户组</th><td>商户管理员</td></tr>'
                            '</table>';

                        $('.pop_showbrand').find('div').html(html);
                        $('.pop_hide').slideDown();
                        $('.pop_showbrand').slideDown();
                    }else{
                        alert(msg.data);
                    }
            }
        });
    });

    /*关闭弹出框*/
    $('.close').click(function(){
       $('.pop_hide').slideUp();
       $('.pop_showbrand').slideUp();
    });

Js;
$this->registerJs($Js);
?>