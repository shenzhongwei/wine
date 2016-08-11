/**
 * Created by 沈小鱼 on 2016/7/25.
 */

$(function(){
    $(document).ready( function(){
        if(window.location.href!=toRoute('index/welcome')){
            layer.closeAll();
            layer.load(2,{
                time: 10*1000,
                shade: [0.1,'#fff']
            });
        };
    });
    $(window).load(function(){
        if(window.location.href!=toRoute('index/welcome')){
            layer.closeAll();
        }
    });
});

function ShowLoad(){
    layer.closeAll();
    layer.load(2,{
        time: 10*1000,
        shade: [0.1,'#fff']
    });
}


function ShowMessage(status,message){
    layer.closeAll('loading');
    if(status == '200'){
        layer.msg(message,{
            icon: 6,
            time: 1500, //2秒关闭（如果不配置，默认是3秒）
        });
    }else if(status == '400'){
        layer.msg(message,{
            icon: 5,
            time: 2000, //2秒关闭（如果不配置，默认是3秒）
        });
    }else {
        layer.msg(message,{
            icon: 0,
            time: 2000, //2秒关闭（如果不配置，默认是3秒）
        });
    }
}

function toRoute(val){
    var url = document.URL;
    var path = url.split('web');
    return path[0]+'web'+'/'+val;
}