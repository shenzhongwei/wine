/**
 * Created by 11633 on 2016/11/4.
 */

$(function () {

    var id = $("#promotioninfo-id").val();
    if(id!=''){
        Promotion(id)
    }
    $("input[name='PromotionInfo[date_valid]']").on("change",function () {
        var date_valid = $(this).val();
        if(date_valid == '1'){
            $('#promotioninfo-start_at').attr('placeholder','请选择活动开始日期');
            $('#promotioninfo-end_at').attr('placeholder','请选择活动结束日期');
            $('#promotioninfo-start_at').attr('disabled',false);
            $('#promotioninfo-end_at').attr('disabled',false);
            $('#promotioninfo-start_at').val('');
            $('#promotioninfo-end_at').val('');
        }else if (date_valid == '0'){
            $('#promotioninfo-start_at').attr('placeholder','该形式无需选择开始日期');
            $('#promotioninfo-end_at').attr('placeholder','该形式无需选择结束日期');
            $('#promotioninfo-start_at').attr('disabled',true);
            $('#promotioninfo-end_at').attr('disabled',true);
            $('#promotioninfo-start_at').val('');
            $('#promotioninfo-end_at').val('');
        }
    });
    $("input[name='PromotionInfo[time_valid]']").on("change",function () {
        var time_valid = $(this).val();
        if(time_valid == '1'){
            $('#promotioninfo-time').attr('placeholder','输入可参与次数');
            $('#promotioninfo-time').attr('disabled',false);
            $('#promotioninfo-time').val('');
        }else if (time_valid == '0'){
            $('#promotioninfo-time').attr('placeholder','该形式无需输入参与次数');
            $('#promotioninfo-time').attr('disabled',true);
            $('#promotioninfo-time').val('');
        }
    });
    $("input[name='PromotionInfo[circle_valid]']").on("change",function () {
        var circle_valid = $(this).val();
        if(circle_valid == '1'){
            $('#promotioninfo-valid_circle').attr('placeholder','输入优惠券有效期(单位：天)');
            $('#promotioninfo-valid_circle').attr('disabled',false);
            $('#promotioninfo-valid_circle').val('');
        }else if (circle_valid == '0'){
            $('#promotioninfo-valid_circle').attr('placeholder','该形式无需输入优惠券的有效期');
            $('#promotioninfo-valid_circle').attr('disabled',true);
            $('#promotioninfo-valid_circle').val('');
        }
    });
    $('#promotioninfo-style').on("change",function () {
        var style=$(this).val();
        var type = $('#select2-promotioninfo-pt_id-container').contents().filter(function(){
            return this.nodeType == 3;
        }).text();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            statusCode: {
                302: function() {
                    layer.alert('登录信息已过期，请重新登录',{icon: 0},function(){
                        window.top.location.href=toRoute('site/login');
                    });
                    return false;
                }
            }
        });
        if(style == '1'){
            $('#promotioninfo-discount').val('');
            $('#promotioninfo-condition').val('');
            $('#promotioninfo-discount').attr('disabled',false);
            $('#promotioninfo-condition').attr('disabled',false);
            $('#promotioninfo-condition').attr('placeholder','输入优惠条件');
            $('#promotioninfo-discount').attr('placeholder','输入优惠额度');
            $.post(toRoute('promotion/style-change'),{
                'type':type,
                '_wine-admin':csrfToken,
            },function(data){
                if(data.status == '302'){
                    layer.alert('登录信息已过期，请重新登录',{icon: 0},function(){
                        window.top.location.href=toRoute('site/login');
                    });
                    return false;
                }else if(data.status == '200'){
                    result = data.data;
                    if(result=='1'){
                        $('#promotioninfo-discount').attr('disabled',true);
                        $('#promotioninfo-discount').attr('placeholder','该优惠形式无需输入优惠额度');
                    }
                }else{
                    layer.alert('数据出错，请重试',{icon: 0});
                    return false;
                }
            },'json');
        }else if(style == '2'){
            $('#promotioninfo-discount').val('');
            $('#promotioninfo-condition').val('');
            $('#promotioninfo-discount').attr('disabled',false);
            $('#promotioninfo-condition').attr('disabled',true);
            $('#promotioninfo-condition').attr('placeholder','该优惠形式无需输入条件');
            $('#promotioninfo-discount').attr('placeholder','输入所占百分比');
        }else if (style == ''){
            $('#promotioninfo-discount').val('');
            $('#promotioninfo-condition').val('');
            $('#promotioninfo-discount').attr('disabled',true);
            $('#promotioninfo-condition').attr('disabled',true);
            $('#promotioninfo-condition').attr('placeholder','请先选择优惠形式');
            $('#promotioninfo-discount').attr('placeholder','请先选择优惠形式');
        }

    });

});


function TypeChange(obj) {
    var type = $(obj).val();

    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    $.ajax({
        statusCode: {
            302: function() {
                layer.alert('登录信息已过期，请重新登录',{icon: 0},function(){
                    window.top.location.href=toRoute('site/login');
                });
                return false;
            }
        }
    });
    $.post(toRoute('promotion/type-change'),{
        'type':type,
        '_wine-admin':csrfToken,
    },function(data){
        if(data.status == '302'){
            layer.alert('登录信息已过期，请重新登录',{icon: 0},function(){
                window.top.location.href=toRoute('site/login');
            });
            return false;
        }else if(data.status == '200'){
            result = data.data;
            if(result.is_time=='0'){
                //限制一次一次，不可操作
                $('input[name="PromotionInfo[time_valid]"][value="0"]').removeAttr('checked');
                $('input[name="PromotionInfo[time_valid]"][value="1"]').prop('checked',true);
                $('#promotioninfo-time').val('1');
                $('#promotioninfo-time').attr('disabled',true);
                $('input[name="PromotionInfo[time_valid]"]').attr('disabled',true);
                $('input[name="PromotionInfo[time_valid]"]').val(1);
            }else if (result.is_time == '1'){
                //限制次数，可操作
                $('input[name="PromotionInfo[time_valid]"][value="0"]').removeAttr('checked');
                $('input[name="PromotionInfo[time_valid]"][value="1"]').removeAttr('checked');
                $('#promotioninfo-time').val('');
                $('#promotioninfo-time').attr('placeholder','请先选择参与次数形式');
                $('#promotioninfo-time').attr('disabled',true);
                $('input[name="PromotionInfo[time_valid]"]').removeAttr('disabled');
            }else {
                //不限制次数，无需操作
                $('input[name="PromotionInfo[time_valid]"][value="0"]').prop('checked',true);
                $('input[name="PromotionInfo[time_valid]"][value="1"]').removeAttr('checked');
                $('#promotioninfo-time').val('');
                $('#promotioninfo-time').attr('placeholder','该形式无需输入参与次数');
                $('#promotioninfo-time').attr('disabled',true);
                $('input[name="PromotionInfo[time_valid]"]').attr('disabled',true);
                $('input[name="PromotionInfo[time_valid]"]').val(0);
            }
            if(result.is_ticket == '0'){
                //非券形式，无需操作
                $('input[name="PromotionInfo[circle_valid]"][value="0"]').removeAttr('checked');
                $('input[name="PromotionInfo[circle_valid]"][value="1"]').removeAttr('checked');
                $('input[name="PromotionInfo[circle_valid]"]').attr('disabled',true);
                $('input[name="PromotionInfo[circle_valid]"]').val(0);
                $('#promotioninfo-valid_circle').val('');
                $('#promotioninfo-valid_circle').attr('placeholder','该形式无需输入优惠券的有效期');
                $('#promotioninfo-valid_circle').attr('disabled',true);
            }else{
                //券形式，可操作
                $('input[name="PromotionInfo[circle_valid]"][value="0"]').removeAttr('checked');
                $('input[name="PromotionInfo[circle_valid]"][value="1"]').removeAttr('checked');
                $('input[name="PromotionInfo[circle_valid]"]').attr('disabled',false);
                $('#promotioninfo-valid_circle').val('');
                $('#promotioninfo-valid_circle').attr('placeholder','请先选择参与优惠券期限形式');
                $('#promotioninfo-valid_circle').attr('disabled',true);
            }
            return false;
        }else{
            layer.alert('数据出错，请重试',{icon: 0});
            return false;
        }
    },'json');
}

function Promotion(id) {

    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    $.ajax({
        statusCode: {
            302: function() {
                layer.alert('登录信息已过期，请重新登录',{icon: 0},function(){
                    window.top.location.href=toRoute('site/login');
                });
                return false;
            }
        }
    });
    $.post(toRoute('promotion/promotion'),{
        'id':id,
        '_wine-admin':csrfToken
    },function(data){
        if(data.status == '302'){
            layer.alert('登录信息已过期，请重新登录',{icon: 0},function(){
                window.top.location.href=toRoute('site/login');
            });
            return false;
        }else if(data.status == '200'){
            var result = data.data;
            var ticket = result.ticket;
            var time = result.time;
            //处理次数
            $('input[name="PromotionInfo[time_valid]"]').attr('disabled',time.is_time=='1' ? false:true);
            $('input[name="PromotionInfo[time_valid]"][value="0"]').removeAttr('checked');
            $('input[name="PromotionInfo[time_valid]"][value="1"]').removeAttr('checked');
            $('input[name="PromotionInfo[time_valid]"][value="'+time.time_check+'"]').prop('checked',true);
            $('#promotioninfo-time').prop('value',time.time_value);
            $('#promotioninfo-time').attr('disabled',time.time_disable=='1' ? true:false);
            $('#promotioninfo-time').attr('placeholder',time.time_placeholder);
            //处理优惠券
            $('input[name="PromotionInfo[circle_valid]"]').attr('disabled',ticket.is_ticket=='1' ? false:true);
            $('input[name="PromotionInfo[circle_valid]"][value="0"]').removeAttr('checked');
            $('input[name="PromotionInfo[circle_valid]"][value="1"]').removeAttr('checked');
            $('input[name="PromotionInfo[circle_valid]"][value="'+ticket.ticket_check+'"]').prop('checked',true);
            $('#promotioninfo-valid_circle').val(ticket.ticket_value);
            $('#promotioninfo-valid_circle').attr('disabled',ticket.ticket_disable=='1' ? true:false);
            $('#promotioninfo-valid_circle').attr('placeholder',ticket.ticket_placeholder);
            return false;
        }else{
            layer.alert('数据出错，请重试',{icon: 0});
            return false;
        }
    },'json');
}
