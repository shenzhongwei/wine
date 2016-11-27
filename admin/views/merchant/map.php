<?php
use kartik\helpers\Html;
?>
<div id="container">
    <div id="search">

        <div id="shop-lication" style="display: none">
            <input id="province" type="text" />
            <input id="city" type="text" />
            <input id="district" type="text" />
            <input id="region" type="text" />
            <input id="lng" type="text" />
            <input id="lat" type="text" />
            <input id="address" type="text" />
        </div>
        <input class="form-control" id="search_input" placeholder="输入您要搜的地址" />
    </div>
</div>
<script type="text/javascript">
    $(function(){
        shop_lat = ($('#merchantinfo-lat').val())/1000000;
        shop_lng = ($('#merchantinfo-lng').val())/1000000;
        shop_name = $('#merchantinfo-name').val();
        shop_limit = $('#merchantinfo-limit').val()*1;
        shop_region = $('#merchantinfo-region').val();
        shop_address = $('#merchantinfo-address').val();
        $(document).ready(init(shop_lat,shop_lng,shop_name,shop_limit,shop_region,shop_address));

        $('#confirm').on('click',function () {
            $('#merchantinfo-province').val($('#province').val());
            $('#merchantinfo-city').val($('#city').val());
            $('#merchantinfo-district').val($('#district').val());
            $('#merchantinfo-region').val($('#region').val());
            $('#merchantinfo-address').val($('#address').val());
            $('#merchantinfo-lat').val($('#lat').val());
            $('#merchantinfo-lng').val($('#lng').val());
        });
    });
    function init(shop_lat,shop_lng,shop_name,shop_limit,shop_region,shop_address) {
        var map,toolbar,scale,auto,lngLat,marker,circle;

        //加载地图，调用浏览器定位服务
        map = new AMap.Map('container', {
            resizeEnable: true
        });
        //输入提示
        auto = new AMap.Autocomplete({
            input:"search_input",
            datatype:'poi-190000'
        });
        //绑定选择事件
        auto.on('select',function (e) {
            if(e.poi.location==''){
                layer.alert('该地址范围模糊，请输入精确地址');
                return false;
            }
            marker.setMap(map);
            marker.setPosition(e.poi.location);
            circle.setCenter(e.poi.location);
            map.setZoomAndCenter(14,e.poi.location);
            map.panTo(e.poi.location);
            var lng = marker.getPosition().getLng();
            var lat = marker.getPosition().getLat();
            $('#lat').val(lat*1000000);
            $('#lng').val(lng*1000000);
            Geocoder(map,new AMap.LngLat(lng,lat),auto,2);
            $('#address').val(e.poi.name);
        });
        //范围圈
        circle = new AMap.Circle({
            map:map,
            strokeColor: "#63B8FF", //线颜色
            strokeOpacity: 0.5, //线透明度
            strokeWeight: 1.5, //线粗细度
            fillColor: "#63B8FF", //填充颜色
            fillOpacity: 0.2//填充透明度
        });
        if(!isNaN(shop_limit)&&shop_limit>0){
            circle.setRadius(shop_limit);
        }else{
            circle.setRadius(0);
        }
        //标注
        marker = new AMap.Marker({
            draggable:true,
            clickable:true
        });
        //拖拽事件
        marker.on('dragging', function() {
            var pos = marker.getPosition();
            circle.setCenter(pos);
            $('#lat').val((pos.getLat()*1000000));
            $('#lng').val((pos.getLng()*1000000));
            Geocoder(map,pos,auto,2);
        });
        if(shop_name!=''){
            marker.setTitle(shop_name);
        }
        //分辨是否已经选择了地址
        if(isNaN(shop_lat)||shop_lat==0||isNaN(shop_lng)||shop_lng==0){
            map.setZoom(10);
            CitySearch(map,auto);
        }else{
            lngLat = new AMap.LngLat(shop_lng,shop_lat);
            map.setZoomAndCenter(14,lngLat);
            Geocoder(map,lngLat,auto,1);
            map.panTo(lngLat);
            marker.setMap(map);
            marker.setPosition(lngLat);
            circle.setCenter(lngLat);
            $('#region').val(shop_region);
            $('#address').val(shop_address);
            $('#lat').val(shop_lat*1000000);
            $('#lng').val(shop_lng*1000000);
        }
        //增加控件
        map.plugin(['AMap.Scale'], function() {
            scale = new AMap.Scale();
            scale.show();
            map.addControl(scale);
        });
        map.plugin(["AMap.ToolBar"],function(){
            //加载工具条
            toolbar = new AMap.ToolBar({
                liteStyle:true
            });
            map.addControl(toolbar);
        });
    }

    function Geocoder(map,lngLat,auto,type) {
        map.plugin(['AMap.Geocoder'], function() {
            var geocoder = new AMap.Geocoder({
                extensions: "base"
            });
            geocoder.getAddress(lngLat, function(status, result) {
                if (status === 'complete' && result.info === 'OK') {
                    auto.setCity(result.regeocode.addressComponent.city);
                    $('#province').val(result.regeocode.addressComponent.province);
                    $('#city').val(result.regeocode.addressComponent.city);
                    $('#district').val(result.regeocode.addressComponent.district);
                    var region = result.regeocode.addressComponent.township;
                    var formatted_address=result.regeocode.formattedAddress;
                    var index = formatted_address.lastIndexOf(region);
                    if(type == 2){
                        $('#region').val(region);
                        if(index>0){
                            $('#address').val(formatted_address.substring(index+(region.length)));
                        }
                    }
                }
            });
        });
    }
    function CitySearch(map,auto) {
        map.plugin(['AMap.CitySearch'], function() {
            //实例化城市查询类
            var citysearch = new AMap.CitySearch();
            //自动获取用户IP，返回当前城市
            citysearch.getLocalCity(function(status, result) {
                if (status === 'complete' && result.info === 'OK') {
                    if (result && result.city) {
                        var cityinfo = result.city;
                        auto.setCity(cityinfo);
                    }
                }
            });
        });
    }
</script>