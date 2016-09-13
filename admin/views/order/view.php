<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use admin\models\OrderInfoSearch;


$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '订单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//购买的商品表
$table='<table class="table2" >
            <tr>
                <th>商品名</th><th>数量</th><th>单价(元)</th>
            </tr>
       ';
foreach($model->orderDetails as $k){
    $table.='<tr><td>'.$k->g->name.'</td><td>'.$k->amount.$k->g->unit.'</td><td>'.$k->single_price.'</td></tr>';
}
$table.='<tr><th colspan="3">
                总价：<span style="color: red">'.$model->total.'</span> 元,
                优惠金额：<span style="color: red">'.$model->discount.'</span> 元,
                配送金额：<span style="color: red">'.$model->send_bill.'</span> 元,
                付款金额：<span style="color: red">'.$model->pay_bill.'</span> 元
             </th></tr></table>';

//用户的评价
$comment='<div class="comment">
             <ul>';
foreach($model->orderComments as $k){
    foreach($k->commentDetails as $key=>$value){
        if(empty($k->u->head_url)){
            $img="../../../photo/logo/user_default.jpg";
        }else{
            $img="../../../photo/".$k->u->head_url;
        }
    $comment.= '<li>
                    <img src="'.$img.'">
                    <div class="area">
                       <p style="width:20%;"><span style="color:#00a2d4;">'.$k->u->nickname.'</span>    <span style="float:right;color:#00a2d4;">'.date('Y-m-d H:i:s',$k->add_at).'</span></p>
                       <div style="margin-bottom:5px;">
                            <div style="height:20px;"><span>送货评分：</span><div class="atar_Show"><p class="'.$k->send_star.'"></p></div></div>
                            <div style="height:20px;"><span> 商品评分：</span><div class="atar_Show"><p class="'.$value['star'].'"></p></div></div>
                       </div>
                       <p style="color:#0a6aa1;">内容'.$value['content'].'</p>
                    </div>
                 </li>';
    }
}
$comment.= '</ul>
          </div>';
?>
<style>

    /*评价css*/
    .comment{ display:none;border: 1px solid #00a2d4;height:auto;border-radius: 5px;}
    .comment ul{padding-left: 5px;list-style: none;}
    .comment ul li{width:95%;margin-top: 5px;min-height:50px;border-bottom: 2px solid #c0c4cd;list-style: none}
     img{width: 50px;height: 50px;border-radius: 25px; border:1px solid #9acfea;}
    .comment div.area{min-height:40px;margin-top:-50px ;margin-left:60px}
    /*评分星级*/
     .atar_Show{background:url('../images/starky.png') 100% 100%; width:110px; height:20px;  position:relative;left:60px;top:-20px;}
    .atar_Show p{ background:url('../images/starsy.png')  100% 100%;left:0; height:20px; width:110px;}

</style>
<!--引用css-->
<?=Html::cssFile('@web/css/wine/table.css')?>
<div class="order-info-view">

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
                'label'=>'门店',
                'attribute'=>'sid',
                'value'=>$model->s->name

            ],
            [
                'label'=>'下单用户',
                'attribute'=>'uid',
                'value'=>$model->u->nickname
            ],
            [
                'label'=>'订单地址',
                'attribute'=>'aid',
                'value'=>empty($model->aid)?'':$model->a->province.'-'.$model->a->city.'-'.$model->a->district.'-'.$model->a->region.$model->a->address

            ],
            [
                'attribute'=>'order_code',
                'format'=>'html',
                'value'=>'<span style="color: #0a6aa1">'.$model->order_code.'</span>'
            ],
            [
                'attribute'=>'order_date',
                'format'=>['date','php:Y-m-d H:i:s' ],
                'value'=>$model->order_date
            ],
            [
                'attribute'=>'pay_id',
                'format'=>'html',
                'value'=>empty($model->pay_id)?'未支付':OrderInfoSearch::getPaytype($model->pay_id).' ( 支付时间：'.$model->pay_date.')'
            ],
            [
                'label'=>'购买的商品',
                'format'=>'html',
                'value'=>$table
            ],
            [
                'attribute'=> 'state',
                'value'=>OrderInfoSearch::getOrderstep($model->state)
            ],
            [
                'attribute'=>'send_code',
                'format'=>'html',
                'value'=>'<span style="color: #0a6aa1">'.$model->send_code.'</span> ( 配送人员 ：<b>'.$model->send['name'].'</b>;联系方式：<b>'.$model->send['phone'].'</b>&nbsp;&nbsp;&nbsp;送达时间：<b>'.(empty($model->send_date)?'':date('Y-m-d H:i:s',$model->send_date)).'</b>) '
            ],
            [
                'attribute'=> 'status',
                'value'=>empty($model->status)?'删除':"正常"
            ],
            [
                'label'=>'订单评价',
                'format'=>'html',
                'value'=>'<a><i class="fa fa-hand-o-down">展开</i></a>'.$comment
            ],
        ],
    ]) ?>
    <p style="margin: 0 auto;text-align: center;">
        <?= Html::a('返回', 'javascript:history.go(-1);', ['class' => 'btn btn-primary']) ?>
    </p>
</div>
<script>
   $(function(){
       $('table a').click(function(){
           var text=$(this).text();//获取a标签下的值
           if(text=='展开'){  //查看评价
               $(this).html('<i class="fa fa-hand-o-up">收起</i>');
               $('div.comment').slideDown();
           }else{  //隐藏评价
               $(this).html('<i class="fa fa-hand-o-down">展开</i>');
               $('div.comment').slideUp();
           }
       });


       //显示分数
       $(".atar_Show p").each(function(index, element) {
            var num=$(this).attr("class");
            var www=num*22;//一个星星的宽度是22px
            $(this).css({width:www+'px'});
       });

   });
</script>