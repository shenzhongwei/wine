<?php

use yii\helpers\Html;
use \kartik\detail\DetailView;
use admin\models\OrderInfo;
use yii\data\ActiveDataProvider;
use admin\models\OrderDetail;
/**
 * @var yii\web\View $this
 * @var admin\models\OrderInfo $model
 */

$this->title = '订单详情:'.$model->order_code;
$this->params['breadcrumbs'][] = ['label' => '信息列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\admin\assets\AppAsset::register($this);
$admin = Yii::$app->user->identity;
$typeArr = [1=>'普通订单','2'=>'会员订单','3'=>'抢购订单'];
$payArr = [1=>'余额支付','2'=>'支付宝支付','3'=>'微信支付'];
?>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=<?=Yii::$app->params['key'] ?>"></script>
<style>
    #address {
        height:100%;
    }
    .info {
        border: solid 1px silver;
    }
    div.info-top {
        position: relative;
        background: none repeat scroll 0 0 #F9F9F9;
        border-bottom: 1px solid #CCC;
        border-radius: 5px 5px 0 0;
    }
    div.info-top div {
        display: inline-block;
        color: #333333;
        font-size: 14px;
        font-weight: bold;
        line-height: 31px;
        padding: 0 10px;
    }
    div.info-top img {
        position: absolute;
        top: 10px;
        right: 10px;
        transition-duration: 0.25s;
    }
    div.info-top img:hover {
        box-shadow: 0px 0px 5px #000;
    }
    div.info-middle {
        font-size: 12px;
        padding: 6px;
        line-height: 20px;
    }
    div.info-bottom {
        height: 0px;
        width: 100%;
        clear: both;
        text-align: center;
    }
    div.info-bottom img {
        position: relative;
        z-index: 104;
    }
    .loc {
        margin-left: 5px;
        font-size: 8px;
    }
    .info-middle img {
        float: left;
        margin-right: 6px;
    }
</style>
<div class="ibox-content">
    <div class="order-info-view">
        <h1><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span> <?= Html::encode($this->title) ?></h1>
        <p>
            <?php

            if($model->status == 0){
                echo Html::a(Yii::t('app','Recover'), ['delete', 'id' => $model->id], [
                    'title' => Yii::t('app', '还原订单'),
                    'class' => 'btn btn-success btn-xs',
                    'data-confirm' => '确认还原该订单吗？',
                ]);
            }else{
                echo Html::a(Yii::t('app','Delete'), ['delete', 'id' => $model->id], [
                    'title' => Yii::t('app', '删除订单'),
                    'class' => 'btn btn-danger btn-xs',
                    'data-confirm' => '确认删除该订单吗？',
                ]);
            }
            if(in_array($model->state,[2,3,4])){
                if($model->state == 2){
                    echo Html::a(Yii::t('app','Receive'),['receive','id'=>$model->id], [
                        'title' => Yii::t('app', '接单'),
                        'class' => 'btn btn-primary btn-xs',
                        'data-confirm' => '确定接单吗',
                        'style'=>'margin-left:0.2%',
                    ]);
                }elseif($model->state == 3){
                    echo Html::a(Yii::t('app','Truck'),['#'], [
                        'title' => Yii::t('app', '发货'),
                        'class' => 'btn btn-success btn-xs send',
                        'data-toggle' => 'modal',    //弹框
                        'data-target' => '#send-modal',    //指定弹框的id
                        'data-id' => $model->id,
                        'style'=>'margin-left:0.2%',
                    ]);
                }else{
                    echo Html::a(Yii::t('app','Arrive'),['arrive','id'=>$model->id], [
                        'title' => Yii::t('app', '已送达'),
                        'class' => 'btn btn-default btn-xs',
                        'data-confirm' => '确定已送达吗',
                        'style'=>'margin-left:0.2%',
                    ]);
                }
            }
            ?>
        </p>
<div class="row">
    <div class="col col-lg-4"><?= DetailView::widget([
            'options'=>[
                'style'=>'height:310px'
            ],
            'model'=>$model,
            'condensed'=>true,
            'striped'=>false,
            'mode'=>DetailView::MODE_VIEW,
            'attributes' => [
                [
                    'label'=>'订单编号',
                    'attribute'=>'order_code',
                    'value'=> $model->order_code,
                ],
                [
                    'label'=>'下单手机',
                    'attribute'=>'uid',
                    'format' => 'raw',
                    'value'=> $admin->wa_type>3 ? $model->u->phone:Html::a($model->u->phone,['user/view', 'id' => $model->uid], ['title' => '查看用户信息','class'=>'btn-link btn-xs']),
                ],
                [
                    'label'=>'下单时间',
                    'attribute'=>'order_date',
                    'format' => ["date", "php:Y-m-d H:i:s"],
                    'value'=> $model->order_date,
                ],
                [
                    'label'=>'所属门店',
                    'attribute'=>'sid',
                    'format' => 'raw',
                    'value'=> $admin->wa_type>3 ? $model->s->name:Html::a($model->s->name,['user/view', 'id' => $model->sid], ['title' => '查看门店信息','class'=>'btn-link btn-xs']),
                ],
                [
                    'label'=>'订单类型',
                    'attribute'=>'type',
                    'format' => 'raw',
                    'value'=> $model->type==1 ? '<label class="label label-info">'.$typeArr[$model->type].'</label>' : ($model->type==2 ?
                        '<label class="label label-success">'.$typeArr[$model->type].'</label>' :
                        '<label class="label label-primary">'.$typeArr[$model->type].'</label>' ),
                ],
                [
                    'label'=>'订单进度',
                    'attribute'=>'state',
                    'format' => 'raw',
                    'value'=> OrderInfo::getOrderstep($model->state),
                ],
                [
                    'label'=>'后台订单状态',
                    'attribute'=>'status',
                    'format' => 'raw',
                    'value' => $model->status==0 ? '<label class="label label-danger">报表不可见</label>':'<label class="label label-success">报表可见</label>'
                ],
                [
                    'label'=>'用户订单状态',
                    'attribute'=>'is_del',
                    'format' => 'raw',
                    'value' => $model->is_del==0 ? '<label class="label label-success">用户可见</label>':'<label class="label label-danger">用户不可见</label>'
                ],
            ],
            'hAlign' =>DetailView::ALIGN_MIDDLE,
            'vAlign' =>DetailView::ALIGN_CENTER,
        ]) ?></div>
    <div class="col col-lg-4"><?= DetailView::widget([
            'options'=>[
                'style'=>'height:310px'
            ],
            'model'=>$model,
            'condensed'=>true,
            'striped'=>false,
            'mode'=>DetailView::MODE_VIEW,
            'attributes' => [
                [
                    'label'=>'付款方式',
                    'attribute'=>'pay_id',
                    'format' => 'raw',
                    'value'=> $model->pay_id==1 ? '<label class="label label-info">'.$payArr[$model->pay_id].'</label>' : ($model->pay_id==2 ?
                        '<label class="label label-success">'.$payArr[$model->pay_id].'</label>' :
                        '<label class="label label-primary">'.$payArr[$model->pay_id].'</label>' ),
                ],
                [
                    'label'=>'付款时间',
                    'attribute'=>'pay_date',
                    'format' => 'raw',
                    'value'=>$model->state<2 ? '<span class="not-set">未付款</span>':date('Y-m-d H:i:s'),
                ],
                [
                    'label'=>'订单总金额',
                    'attribute'=>'total',
                ],
                [
                    'label'=>'运费',
                    'attribute'=>'send_bill',
                    'format' => 'raw',
                    'value'=>empty($model->send_bill-0) ? '<span class="not-set">无运费</span>':$model->send_bill,
                ],
                [
                    'label'=>'用券情况',
                    'attribute'=>'ticket_id',
                    'format' => 'raw',
                    'value'=>$model->ticket_id==0 ||empty($model->t)||empty($model->t->p) ? '<span class="not-set">未用券</span>':
                        '<span class="not-set">'.$model->t->p->discount.'元优惠券</span>',
                ],
                [
                    'label'=>'使用积分情况',
                    'attribute'=>'point',
                    'format' => 'raw',
                    'value'=>empty($model->point-0) ? '<span class="not-set">未用积分</span>':
                        '<span class="not-set">'.$model->point.'积分抵'.$model->point.'元</span>',
                ],
                [
                    'label'=>'付款金额',
                    'attribute'=>'pay_bill',
                    'value'=>$model->pay_bill,
                ],
            ],
            'hAlign' =>DetailView::ALIGN_MIDDLE,
            'vAlign' =>DetailView::ALIGN_CENTER,
        ]) ?></div>
    <div class="col col-lg-4">
        <?= DetailView::widget([
            'options'=>[
                'style'=>'height:310px'
            ],
            'model'=>$model,
            'condensed'=>true,
            'striped'=>false,
            'mode'=>DetailView::MODE_VIEW,
            'attributes' => [
                [
                    'label'=>'配送地址',
//                    'attribute'=>'aid',
                    'value'=>'',
                    'valueColOptions'=>[
                        'id'=>'user_address',
                    ],
                ],
            ],
            'hAlign' =>DetailView::ALIGN_MIDDLE,
            'vAlign' =>DetailView::ALIGN_CENTER,
        ]) ?>
    </div>
</div>
        <div class="row">
            <div class="col col-lg-12">
                <?= DetailView::widget([
                    'options'=>[
                    ],
                    'model'=>$model,
                    'condensed'=>true,
                    'striped'=>false,
                    'mode'=>DetailView::MODE_VIEW,
                    'attributes' => [
                        [
                            'label'=>'订单详情',
                            'format' => 'html',
                            'value'=>$this->render('_orderdetail',[
                                'dataProvider'=>new ActiveDataProvider([
                                    'query'=>OrderDetail::find()->where("oid=$model->id"),
                                ])
                            ]),
                        ]
                    ],
                    'hAlign' =>DetailView::ALIGN_MIDDLE,
                    'vAlign' =>DetailView::ALIGN_CENTER,
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col col-lg-12">
            <?= DetailView::widget([
                'options'=>[
                ],
                'model'=>$model,
                'condensed'=>true,
                'striped'=>false,
                'mode'=>DetailView::MODE_VIEW,
                'attributes' => [
                    [
                        'label'=>'配送详情',
                        'format' => 'html',
                        'value'=>$model->state<4||empty($model->send_id) ? '<span class="not-set">尚未配送</span>':(empty($model->send) ?
                            '<span class="not-set">配送人员信息异常</span>':$this->render('_ordersend',[
                                'dataProvider'=>new ActiveDataProvider([
                                    'query'=>OrderInfo::find()->where("id=$model->id"),
                                ])
                            ])),
                    ]
                ],
                'hAlign' =>DetailView::ALIGN_MIDDLE,
                'vAlign' =>DetailView::ALIGN_CENTER,
            ]) ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $(document).ready(init(<?=$model->distance ?>,<?=empty($model->a) ? '':json_encode($model->a->toArray(),true); ?>));
    });
    function init(distance,userAddress) {
        if (userAddress == '') {
            $('#user_address').css('text-align', 'center').html('<span class="not-set">收货地址数据异常</span>')
            return false;
        }
        if (userAddress.lng == '0' || userAddress.lat == '0') {
            $('#user_address').css('text-align', 'center').html('<span class="not-set">收货地址数据丢失</span>')
            return false;
        }
        var map, lngLat, marker;
        lngLat = new AMap.LngLat(userAddress.lng / 1000000, userAddress.lat / 1000000);
        //加载地图，调用浏览器定位服务
        map = new AMap.Map('user_address', {
            resizeEnable: true,
            dragEnable: false,
            zoomEnable: false,
        });
        marker = new AMap.Marker({
            draggable: false,
            clickable: true
        });
        map.setZoomAndCenter(13, lngLat);
        map.panTo(lngLat);
        marker.setMap(map);
        marker.setPosition(lngLat);
        //输入提示
        AMap.event.addListener(marker, 'click', function () {
            infoWindow.open(map, lngLat);
        });
        //实例化信息窗体
        var title = "收货信息<span class='loc'>(" + userAddress.lng / 1000000 + "," + userAddress.lat / 1000000 + ")</span>", content = [];
        content.push("收货人：" + userAddress.get_person);
        content.push("收货电话：" + userAddress.get_phone);
        content.push("直线距离：" + distance+'米');
        content.push("收货地址：" + userAddress.province + userAddress.city + userAddress.district + userAddress.region + userAddress.address);
        var infoWindow = new AMap.InfoWindow({
            isCustom: true,  //使用自定义窗体
            content: createInfoWindow(title, content.join("<br/>")),
            offset: new AMap.Pixel(16, -45)
        });
        //构建自定义信息窗体
        function createInfoWindow(title, content) {
            var info = document.createElement("div");
            info.className = "info";

            //可以通过下面的方式修改自定义窗体的宽高
            //info.style.width = "400px";
            // 定义顶部标题
            var top = document.createElement("div");
            var titleD = document.createElement("div");
            var closeX = document.createElement("img");
            top.className = "info-top";
            titleD.innerHTML = title;
            closeX.src = "http://webapi.amap.com/images/close2.gif";
            closeX.onclick = closeInfoWindow;

            top.appendChild(titleD);
            top.appendChild(closeX);
            info.appendChild(top);

            // 定义中部内容
            var middle = document.createElement("div");
            middle.className = "info-middle";
            middle.style.backgroundColor = 'white';
            middle.innerHTML = content;
            info.appendChild(middle);

            // 定义底部内容
            var bottom = document.createElement("div");
            bottom.className = "info-bottom";
            bottom.style.position = 'relative';
            bottom.style.top = '0px';
            bottom.style.margin = '0 auto';
            var sharp = document.createElement("img");
            sharp.src = "http://webapi.amap.com/images/sharp.png";
            bottom.appendChild(sharp);
            info.appendChild(bottom);
            return info;
        }

        //关闭信息窗体
        function closeInfoWindow() {
            map.clearInfoWindow();
        }
    }
</script>
