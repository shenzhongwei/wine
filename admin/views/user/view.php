<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use admin\models\UserInfo;
use admin\models\OrderInfoSearch;
use admin\models\UserAccount;
use kartik\grid\GridView;
/**
 * @var yii\web\View $this
 * @var admin\models\UserInfo $model
 */

$this->title = $model->nickname;
$this->params['breadcrumbs'][] = ['label' => '用户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//用户地址
if(!empty($model->userAddresses)){
    $addresstable='<table class="table2" >
            <tr>
                <th>用户名</th><th>联系方式</th><th>收货地址</th><th>是否默认地址</th>
            </tr>
       ';
    foreach($model->userAddresses as $k){
        $addresstable.='<tr>
        <td>'.$k->get_person.'</td>
        <td>'.$k->get_phone.'</td>
        <td>'.$k->province.'-'.$k->city.'-'.$k->district.'-'.$k->region.'-'.$k->address.'</td>
        <td>'.(empty($k->is_default)?'':'<i class="fa fa-circle" style="color: red">是</i>').'</td>
        </tr>';
    }
    $addresstable.='</table>';
}else{
    $addresstable='无';
}

//用户订单
if(!empty($model->orderInfos)){
    $ordertable='<table class="table2" >
            <tr>
                <th>下单时间</th><th>订单号</th><th>总金额</th><th>优惠金额</th><th>付款金额</th><th>物流编码</th><th>订单进度</th><th>操作</th>
            </tr>';
    foreach($model->orderInfos as $k){
        $ordertable.='<tr>
        <td>'.date('Y-m-d H:i:s',$k->order_date).'</td>
        <td>'.$k->order_code.'</td>
        <td>'.$k->total.'</td>
        <td>'.$k->discount.'</td>
        <td>'.$k->pay_bill.'</td>
        <td>'.$k->send_code.'</td>
        <td>'.OrderInfoSearch::getOrderstep($k->state).'</td>
        <td><a href="../order/view?id='.$k->id.'"><i class="fa fa-hand-o-right">详情</i></a></td>
        </tr>';
    }
    $ordertable.='</table>';
}else{
    $ordertable='无';
}

//账户余额
$useraccount=UserAccount::find()->where(['target'=>$model->id,'level'=>2,'type'=>1])->one();
?>
<!--引用css-->
<?=Html::cssFile('@web/css/wine/table.css')?>
<div class="wrapper wrapper-content">
    <div class="ibox-content">
<div class="user-info-view">
    <?= DetailView::widget([
        'model' => $model,
        'hover'=>true,
        'mode'=>false,
        'hAlign' =>DetailView::ALIGN_MIDDLE,
        'panel'=>[
            'heading'=>'详细信息：'.$this->title,
            'type'=>DetailView::TYPE_INFO,
            'headingOptions'=>[
                'template'=>'{title}'
            ]
        ],
        'attributes' => [
            [
                'attribute'=>'nickname',
            ],
            [
                'attribute'=>'phone',
                'label'=>'手机号码',
            ],
            [
                'label'=>'账户余额',
                'value'=>empty($model->a) ? '未开户':$model->a->end.'元',
            ],
            [
                'label'=>'登录信息',
                'format'=>'html',
                'value'=>GridView::begin()
            ],
            'sex',
            [
                'attribute'=>'head_url',
                'format'=>'html',
                'value'=>!empty($model->head_url)?'<img src="../../../photo'.$model->head_url.'" width="50" height="50">':
                        '<img src="../../../photo/logo/user_default.jpg" width="50" height="50">',
            ],
            [
                'attribute'=>'birth',
                'value'=>$model->birth=='0000-00-00'?'':$model->birth,
            ],
            [
                'attribute'=>'invite_user',
                'label'=>'邀请人',
                'value'=> empty($model->invite_user_id)?'':UserInfo::findOne(['id'=>$model->invite_user_id])->realname.'('.UserInfo::findOne(['id'=>$model->invite_user_id])->nickname.')',
            ],
            [
                'attribute'=>'is_vip',
                'value'=>empty($model->is_vip)?'否':'是'
            ],
            'invite_code',
            [
                'attribute'=>'status',
                'format'=>'html',
                'value'=>empty($model->status)?'<p><span class="label label-default"><i class="fa fa-times"></i> 已 删</span></p>':
                    '<p><span class="label label-primary"><i class="fa fa-check"></i> 正常</span></p>',
            ],
            'created_time',
            [
                'label'=>'用户地址',
                'format'=>'html',
                'value'=>$addresstable,
            ],
            [
                'label'=>'用户订单',
                'format'=>'html',
                'value'=>$ordertable,
            ]
        ],

    ]) ?>
    <p style="margin: 0 auto;text-align: center;">
        <?= Html::a('返回', 'javascript:history.go(-1);', ['class' => 'btn btn-primary']) ?>
    </p>
</div></div></div>
