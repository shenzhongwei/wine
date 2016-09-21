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

function clearNoNum(obj){

    obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符

    obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字而不是

    obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的

    obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");

    obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数

}

function toRoute(val){
    var url = document.URL;
    var path = url.split('web');
    return path[0]+'web'+'/'+val;
}