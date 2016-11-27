<?php

use yii\helpers\Html;
use \kartik\detail\DetailView;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;

/**
 * @var yii\web\View $this
 * @var admin\models\MerchantInfo $model
 * @var ActiveDataProvider $orders
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '信息列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\admin\assets\AppAsset::register($this);
// here
$this->registerJsFile("@web/js/good/_script.js");
?>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=<?=Yii::$app->params['key'] ?>"></script>
<style>
    #pic div{
        height:320px;
        width:100%;
        overflow:hidden;
        text-align: center;
    }
    #location {
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
        font-size: 11px;
    }
    .info-middle img {
        float: left;
        margin-right: 6px;
    }
</style>
<div class="">
    <div class="ibox-content">
        <div class="good-info-view">
            <h1><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span> <?= Html::encode($this->title) ?></h1>
            <p>
                <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
                <?= Html::a(Yii::t('app', $model->is_active == 1 ? 'Lock':'Unlock'), ['delete', 'id' => $model->id], [
                    'class' =>  $model->is_active == 1 ? 'btn btn-sm btn-danger':'btn btn-sm btn-info',
                    'data'=>[
                        'confirm'=>$model->is_active == 1 ? '确定冻结该商户，一旦冻结，用户将无法看到该商户下的商品':'确定解除冻结，一旦解除，用户将可以购买该商户下的商品',
                    ],
                ]); ?>
                <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-sm btn-success']) ?>
            </p>

            <div class="row">
                <div class="col-sm-3">
                    <?= DetailView::widget([
                        'model'=>$model,
                        'condensed'=>true,
                        'striped'=>false,
                        'mode'=>DetailView::MODE_VIEW,
                        'options'=>[
                            'style'=>'height:360px'
                        ],
                        'labelColOptions' => ['style' => 'width: 40%'],
                        'attributes' => [

                            [
                                'attribute'=>'name',
                                'value'=> $model->name,
                            ],
                            [
                                'label'=>'后台账号',
                                'attribute'=>'wa_id',
                                'format' => 'raw',
                                'value'=> Html::a($model->wa->wa_name,['manager/update', 'id' => $model->wa->wa_id], ['title' => '查看后台登录信息','class'=>'btn btn-link btn-xs']),
                            ],
                            'contacter',
                            'phone',
                            [
                                'label'=>'状态',
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => $model->is_active==0 ? '<label class="label label-danger">冻结中</label>':'<label class="label label-success">已激活</label>'

                            ],
                            [
                                'label'=>$model->is_active == 0 ? '冻结时间':'激活时间',
                                'attribute'=>'active_at',
                                'format'=>["date", "php:Y年m月d日"],
                                'value'=>$model->active_at,
                            ],
                            [
                                'label'=>'入驻时间',
                                'attribute'=>'registe_at',
                                'format'=>["date", "php:Y年m月d日"],
                                'value'=> $model->registe_at
                            ],
                        ],
                        'hAlign' =>DetailView::ALIGN_MIDDLE,
                        'vAlign' =>DetailView::ALIGN_CENTER,
                    ]) ?>
                </div>
                <div class="col-sm-4">
                    <?= DetailView::widget([
                        'options'=>[
                            'style'=>'height:360px',
                            'id'=>'pic'
                        ],
                        'model'=>$model,
                        'condensed'=>true,
                        'striped'=>false,
                        'mode'=>DetailView::MODE_VIEW,
                        'attributes' => [
                            [
                                'attribute'=>'wa_logo',
                                'label'=>'商户logo',
                                "format" => "raw",
                                'value'=>empty($model->wa_logo) ? '<span class="not-set">未设置</span>':Html::img('../../../../photo'.$model->wa_logo,[
                                    'height'=>"320px","onclick"=>"ShowLogo(this);",'style'=>'cursor:pointer','title'=>"点击放大"
                                ]),
                            ],
                        ],
                        'hAlign' =>DetailView::ALIGN_MIDDLE,
                        'vAlign' =>DetailView::ALIGN_CENTER,
                    ]) ?>
                </div>
                <div class="col-sm-5">
                    <?= DetailView::widget([
                        'options'=>[
                            'style'=>'height:360px'
                        ],
                        'model'=>$model,
                        'condensed'=>true,
                        'striped'=>false,
                        'mode'=>DetailView::MODE_VIEW,
                        'attributes' => [
                            [
                                'label'=>'商户地址',
                                'attribute'=>'region',
                                'valueColOptions'=>[
                                    'id'=>'location',
                                ],
                            ],
                        ],
                        'hAlign' =>DetailView::ALIGN_MIDDLE,
                        'vAlign' =>DetailView::ALIGN_CENTER,
                    ]) ?>
                </div>
            </div>
            <div class="row">
                <?= $this->render('_merchantorder',['dataProvider'=>$orders]) ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $(document).ready(init(<?=json_encode($model->toArray(),true); ?>));
    });
    function init(data) {
        if(data.lng=='0'||data.lat=='0'){
            $('#location').css('text-align','center').html('<span class="not-set">暂未设置地址</span>')
            return false;
        }
       var map,lngLat,marker,circle;
        lngLat = new AMap.LngLat(data.lng/1000000,data.lat/1000000);
        //加载地图，调用浏览器定位服务
        map = new AMap.Map('location', {
            resizeEnable: true,
            dragEnable:false,
            zoomEnable:false
        });
        marker = new AMap.Marker({
            draggable:false,
            clickable:true
        });
        circle = new AMap.Circle({
            map:map,
            strokeColor: "#63B8FF", //线颜色
            strokeOpacity: 0.5, //线透明度
            strokeWeight: 1.5, //线粗细度
            fillColor: "#63B8FF", //填充颜色
            fillOpacity: 0.2//填充透明度
        });
        map.setZoomAndCenter(13,lngLat);
        map.panTo(lngLat);
        marker.setMap(map);
        marker.setPosition(lngLat);
        circle.setCenter(lngLat);
        //输入提示
        AMap.event.addListener(marker, 'click', function() {
            infoWindow.open(map, lngLat);
        });
        //实例化信息窗体
        var title = data.name+"<span class='loc'>("+data.lng/1000000+","+data.lat/1000000+")</span>", content = [];
        content.push("地址："+data.province+data.city+data.district+data.region+data.address);
        content.push("电话："+data.phone);
        content.push("经度："+data.lng/1000000);
        content.push("纬度："+data.lat/1000000);
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
