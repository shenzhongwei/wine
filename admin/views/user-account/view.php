<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use yii\widgets\Pjax;
use kartik\grid\GridView;
/**
 * @var yii\web\View $this
 * @var admin\models\UserAccount $model
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '账户明细', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<!--账户明细-->

<?php Pjax::begin();
$table= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'header'=>'序号',
            'class' => 'yii\grid\SerialColumn'
        ],
        [
            'attribute'=>'aio_date',
            'value'=>function($data){
                return date('Y-m-d H:i:s',$data->aio_date);
            }
        ],
        [
            'attribute'=>'type',
            'value'=>function($data){
                $query=\admin\models\Dics::find()->where(['type'=>'钱包明细类型','id'=>$data->type])->one();
                return empty($query)?'':$query->name;
            },
        ],
        [
            'label'=>'订单号/用户名称',
            'attribute'=>'target_id',
            'format'=>'html',
            'value'=>function($data){
                if($data->type<=2){ //订单号
                    $query=\admin\models\OrderInfo::find()->where(['id'=>$data->target_id])->one();

                    return Html::a((empty($query)?'':$query->order_code),'../order/view?id='.$data->target_id);

                }elseif($data->type>2){ //用户id
                    $query=\admin\models\UserInfo::find()->where(['id'=>$data->target_id])->one();

                   return  Html::a(empty($query)?'':$query->realname,'../user/view?id='.$data->target_id);

                }
            }
        ],
        [
            'label'=>'金额',
            'attribute'=> 'money',
            'format'=>'html',
            'value'=>function($data){
                if($data->type==1){ $f='-';}
                elseif($data->type>=2){ $f='+';}
                return "<h4>".$f.$data->inoutPays->money.'</h4>';
            }
        ],

        [
            'label'=>'支付时间',
            'attribute'=> 'pay_date',
            'value'=>function($data){
                return date('Y-m-d H:i:s',$data->inoutPays->pay_date);
            }
        ],
        [
            'label'=>'支付方式',
            'attribute'=> 'pay_id',
            'value'=>function($data){
                $query = \admin\models\Dics::find()->where(['id' =>$data->inoutPays->pay_id, 'type' => '付款方式'])->one();
                return empty($query) ? '' : $query->name;
            }
        ],
        [
            'label'=>'支付账户',
            'attribute'=>  'account',
            'value'=>function($data){
                return $data->inoutPays->account;

            }
        ],
        [
            'label'=>'流水号',
            'attribute'=>  'transaction_id',
            'value'=>function($data){
                return $data->inoutPays->transaction_id;
            }
        ],
        [
            'label'=>'交易状态',
            'attribute'=>  'status',
            'format'=>'html',
            'value'=>function($data){
                switch($data->status){
                    case 0: $str='删除'; break;
                    case 1: $str='正常'; break;
                    case 2: $str='待付款'; break;
                    default: $str='无'; break;
                }
                return $str;
            }
        ],
    ],
    'responsive'=>true,
    'hover'=>true,
    'condensed'=>true,
    'floatHeader'=>true,
]); Pjax::end(); ?>



<div class="user-account-view">
    <?= DetailView::widget([
        'model' => $model,
        'condensed'=>false,
        'hover'=>true,
        'attributes' => [
            'id',
            [
                'attribute'=>'target_name',
                'value'=>\admin\models\UserAccount::getAccountAcceptName($model).'('.($model->level==1?'管理员':'用户').')',
            ],
            [
                'attribute'=>'type',
                'value'=>\admin\models\Dics::find()->where(['id'=>$model->type,'type'=>'钱包类型'])->one()->name,
            ],
            [
                'attribute'=> 'start',
                'format'=>'html',
                'value'=>'<span style="color: red">'.$model->start.'</span> 元',
            ],
            [
                'attribute'=> 'end',
                'format'=>'html',
                'value'=>'<span style="color: red">'.$model->end.'</span> 元',
            ],

           [
               'label'=>'账户明细',
               'format'=>'html',
               'value'=>$table
           ],
        ],

    ]) ?>

</div>