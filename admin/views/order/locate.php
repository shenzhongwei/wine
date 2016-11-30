<?php
/**
 * @var \admin\models\UserAddress $model
 */
?>
<script type="text/javascript">
    $(function () {
        $(document).ready(init(<?=json_encode($model->toArray(), true); ?>));
    });
    function init(data) {
        if (data.lng == '0' || data.lat == '0') {
            $('#locate').css('text-align', 'center').html('<span class="not-set">暂未设置地址</span>')
            return false;
        }
        var map, lngLat, marker;
        lngLat = new AMap.LngLat(data.lng / 1000000, data.lat / 1000000);
        //加载地图，调用浏览器定位服务
        map = new AMap.Map('locate', {
            resizeEnable: true,
            dragEnable: false,
            zoomEnable: false
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
        var title = data.name + "<span class='loc'>(" + data.lng / 1000000 + "," + data.lat / 1000000 + ")</span>", content = [];
        content.push("地址：" + data.province + data.city + data.district + data.region + data.address);
        content.push("电话：" + data.phone);
        content.push("经度：" + data.lng / 1000000);
        content.push("纬度：" + data.lat / 1000000);
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