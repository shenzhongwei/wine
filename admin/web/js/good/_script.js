function ShowImg(obj){
    path  = obj.src;
    var img = new Image();
    // 开始加载图片
    img.src = path;
// 为Image对象添加图片加载成功的处理方法
    img.onload = function(){
        layer.open({
            area: ['360px', '360px'],
            type:1,
            content:'<div style="text-align: center"><img src="'+path+'" width="360px" height="360px" ></div>',
            title:false,
            scrollbar:false,
            shadeClose:true,
            move :false,
            shift:5,
        });
    }
}

function ShowPic(obj){
    path  = obj.src;
    var img = new Image();
    // 开始加载图片
    img.src = path;
// 为Image对象添加图片加载成功的处理方法
    img.onload = function(){
        layer.open({
            area: ['900px', '600px'],
            type:1,
            content:'<div style="text-align: center"><img src="'+path+'" width="900px" height="600px" ></div>',
            title:false,
            scrollbar:false,
            shadeClose:true,
            move :false,
            shift:5,
        });
    }
}

function ShowBoot(obj){
    path  = obj.src;
    var img = new Image();
    // 开始加载图片
    img.src = path;
// 为Image对象添加图片加载成功的处理方法
    img.onload = function(){
        layer.open({
            area: ['200px', '360px'],
            type:1,
            content:'<div style="text-align: center"><img src="'+path+'" width="200px" height="360px" ></div>',
            title:false,
            scrollbar:false,
            shadeClose:true,
            move :false,
            shift:5,
        });
    }
}

function ShowAd(obj){
    path  = obj.src;
    var img = new Image();
    // 开始加载图片
    img.src = path;
// 为Image对象添加图片加载成功的处理方法
    img.onload = function(){
        layer.open({
            area: ['360px', '200px'],
            type:1,
            content:'<div style="text-align: center"><img src="'+path+'" width="360px" height="200px" ></div>',
            title:false,
            scrollbar:false,
            shadeClose:true,
            move :false,
            shift:5,
        });
    }
}