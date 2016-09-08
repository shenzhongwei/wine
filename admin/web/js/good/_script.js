function ShowImg(obj){
    path  = obj.src;
    var img = new Image();
    // 开始加载图片
    img.src = path;
// 为Image对象添加图片加载成功的处理方法
    img.onload = function(){
        layer.open({
                type:1,
                content:'<img src="'+path+'" height="500px" >',
                title:false,
                shadeClose:true,
                move :false,
                shift:5
            }
        );
    }
}