<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use admin\models\UserInfo;
use admin\models\OrderInfoSearch;
use admin\models\UserAccount;
use admin\models\UserLogin;
use yii\data\ActiveDataProvider;
/**
 * @var yii\web\View $this
 * @var admin\models\UserInfo $model
 * @var ActiveDataProvider $orders
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


//账户余额
$useraccount=UserAccount::find()->where(['target'=>$model->id,'level'=>2,'type'=>1])->one();
?>
<!--引用css-->
<?=Html::cssFile('@web/css/wine/table.css')?>
<div class="user-info-view">
    <div class="panel panel-info">
        <div class="panel-heading">
            <?= ' 详细信息：'.$this->title ?>
        </div>
        <div class="panel-body">
    <div class="row">
        <div class="col-sm-4">
            <?= DetailView::widget([
                'model' => $model,
                'hover'=>false,
                'condensed'=>true,
                'mode'=>false,
                'hAlign' =>DetailView::ALIGN_MIDDLE,
                'panel'=>false,
                'attributes' => [
                    [
                        'attribute'=>'nickname',
                    ],
                    'sex',
                    [
                        'attribute'=>'head_url',
                        'format'=>'html',
                        'value'=>
                            !empty($model->head_url)?'<img src="../../../../photo'.$model->head_url.'" width="50" height="50">':
                            '<img src="../../../../photo/logo/user_default.jpg" width="50px" height="50px">',
                    ],
                    [
                        'attribute'=>'birth',
                        'value'=>$model->birth=='0000-00-00'?'未填写':$model->birth,
                    ],
                    [
                        'attribute'=>'phone',
                        'label'=>'手机号码',
                    ],
                    [
                        'attribute'=>'invite_user',
                        'label'=>'邀请人',
                        'value'=> empty($model->invite_user_id)?'未填写':UserInfo::findOne(['id'=>$model->invite_user_id])->realname.'('.UserInfo::findOne(['id'=>$model->invite_user_id])->nickname.')',
                    ],
                    'invite_code',
                    [
                        'attribute'=>'is_vip',
                        'label'=>'会员状态',
                        'value'=>empty($model->is_vip)?'否':'是'
                    ],
                    [
                        'label'=>'账户余额',
                        'value'=>empty($model->a) ? '未开户':$model->a->end.'元',
                    ],
                    'created_time',
                    [
                        'label'=>'登录信息',
                        'format'=>'html',
                        'value'=>$this->render('_userlogin',[
                            'dataProvider'=>new ActiveDataProvider([
                                'query'=>UserLogin::find()->where(['uid'=>$model->id]),
                            ])
                        ])
                    ],
                    [
                        'attribute'=>'status',
                        'label'=>'状态',
                        'format'=>'html',
                        'value'=>empty($model->status)?'<p><span class="label label-default"><i class="fa fa-times"></i> 冻 结</span></p>':
                            '<p><span class="label label-primary"><i class="fa fa-check"></i> 正 常</span></p>',
                    ],
                ],

            ]) ?>
        </div>
        <div class="col-sm-8">
            <?= DetailView::widget([
                'model' => $model,
                'hover'=>false,
                'condensed'=>true,
                'mode'=>false,
                'hAlign' =>DetailView::ALIGN_MIDDLE,
                'panel'=>false,
                'attributes' => [
                    [
                        'label'=>'用户订单',
                        'labelColOptions'=>['style' => 'width: 10%'],
                        'format'=>'html',
                        'value'=>$this->render('_userorder',['dataProvider'=>$orders]),
                    ]
                ],

            ]) ?>
        </div>
    </div>
    <div class="row col-sm-12">
        <?= DetailView::widget([
            'model' => $model,
            'hover'=>false,
            'condensed'=>true,
            'mode'=>false,
            'hAlign' =>DetailView::ALIGN_MIDDLE,
            'panel'=>false,
            'attributes' => [
                [
                    'label'=>'用户地址',
                    'format'=>'html',
                    'value'=>$addresstable,
                ],
            ],

        ]) ?>
    </div>
</div>
        </div>
