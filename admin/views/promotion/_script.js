$(function () {
    var id = $("#promotioninfo-id").val();
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
    if(id!='' && id!=null && !isNaN(id)){
        $('#promotioninfo-time').val('').removeAttr('placeholder').removeAttr('disabled');
        $('#promotioninfo-valid_circle').val('').removeAttr('placeholder').removeAttr('disabled');
        $('input[name="PromotionInfo[time_valid]"]').removeAttr('disabled');
        $('input[name="PromotionInfo[time_valid]"][value="0"]').removeAttr('checked');
        $('input[name="PromotionInfo[time_valid]"][value="1"]').removeAttr('checked');
        $('input[name="PromotionInfo[circle_valid]"]').removeAttr('disabled');
        $('input[name="PromotionInfo[circle_valid]"][value="0"]').removeAttr('checked');
        $('input[name="PromotionInfo[circle_valid]"][value="1"]').removeAttr('checked');
        $('#promotioninfo-condition').removeAttr('disabled').removeAttr('placeholder').val('');
        $('#promotioninfo-discount').removeAttr('disabled').removeAttr('placeholder').val('');
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
                if( !isNaN(result.timeValidValue)){
                    $('input[name="PromotionInfo[time_valid]"][value="'+result.timeValidValue+'"]').prop('checked',true);
                }
                $('input[name="PromotionInfo[time_valid]"]').attr('disabled',result.timeValidDisable=='1');
                $('#promotioninfo-time').val(result.timeVlaue).attr(
                    'disabled',result.timeDisable=='1').attr('placeholder',result.timePlaceHolder);
                if(!isNaN(result.ticketValidValue)){
                    $('input[name="PromotionInfo[circle_valid]"][value="'+result.ticketValidValue+'"]').prop('checked',true);
                }
                $('input[name="PromotionInfo[circle_valid]"]').attr('disabled',result.ticketValidDisable=='1');
                $('#promotioninfo-valid_circle').val(result.ticketVlaue).attr(
                    'disabled',result.ticketDisable == '1').attr('placeholder',result.ticketPlaceHolder);
                $('#promotioninfo-condition').attr(
                    'disabled',result.conditionDisable=='1').attr('placeholder',result.conditionPlaceholder).val(result.conditionValue);
                $('#promotioninfo-discount').attr(
                    'disabled',result.discountDisable=='1').attr('placeholder',result.discountPlaceholder).val(result.discountValue);
                return false;
            }else{
                layer.alert('数据出错，请重试',{icon: 0});
                return false;
            }
        },'json');
    }
    $('#promotioninfo-pt_id').on("change",function () {
        var type = $(this).val();
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
        if(type != '' && !isNaN(type)){
            $('#promotioninfo-time').val('').removeAttr('placeholder').removeAttr('disabled');
            $('#promotioninfo-valid_circle').val('').removeAttr('placeholder').removeAttr('disabled');
            $('input[name="PromotionInfo[time_valid]"]').removeAttr('disabled');
            $('input[name="PromotionInfo[time_valid]"][value="0"]').removeAttr('checked');
            $('input[name="PromotionInfo[time_valid]"][value="1"]').removeAttr('checked');
            $('input[name="PromotionInfo[circle_valid]"]').removeAttr('disabled');
            $('input[name="PromotionInfo[circle_valid]"][value="0"]').removeAttr('checked');
            $('input[name="PromotionInfo[circle_valid]"][value="1"]').removeAttr('checked');
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
                    var result = data.data;
                    if( !isNaN(result.timeValidValue)){
                        $('input[name="PromotionInfo[time_valid]"][value="'+result.timeValidValue+'"]').prop('checked',true);
                    }
                    $('input[name="PromotionInfo[time_valid]"]').attr('disabled',result.timeValidDisable=='1');
                    $('#promotioninfo-time').val(result.timeVlaue).attr(
                        'disabled',result.timeDisable=='1').attr('placeholder',result.timePlaceHolder);
                    if(!isNaN(result.ticketValidValue)){
                        $('input[name="PromotionInfo[circle_valid]"][value="'+result.ticketValidValue+'"]').prop('checked',true);
                    }
                    $('input[name="PromotionInfo[circle_valid]"]').attr('disabled',result.ticketValidDisable=='1');
                    $('#promotioninfo-valid_circle').val(result.ticketVlaue).attr(
                        'disabled',result.ticketDisable == '1').attr('placeholder',result.ticketPlaceHolder);

                    return false;
                }else{
                    layer.alert('数据出错，请重试',{icon: 0});
                    return false;
                }
            },'json');
        }
    });
    $("input[name='PromotionInfo[date_valid]']").on("change",function () {
        var date_valid = $(this).val();
        $('#promotioninfo-start_at').removeAttr('placeholder').removeAttr('disabled').val('');
        $('#promotioninfo-end_at').removeAttr('placeholder').removeAttr('disabled').val('');
        if(date_valid == '1'){
            $('#promotioninfo-start_at').attr('placeholder','请选择活动开始日期');
            $('#promotioninfo-end_at').attr('placeholder','请选择活动结束日期');
        }else{
            $('#promotioninfo-start_at').attr('placeholder','该形式无需选择开始日期').attr('disabled',true);
            $('#promotioninfo-end_at').attr('placeholder','该形式无需选择结束日期').attr('disabled',true);
        }
    });
    $("input[name='PromotionInfo[time_valid]']").on("change",function () {
        var time_valid = $(this).val();
        $('#promotioninfo-time').removeAttr('placeholder').removeAttr('disabled').val('');
        if(time_valid == '1'){
            $('#promotioninfo-time').attr('placeholder','请输入可参与次数');
        }else{
            $('#promotioninfo-time').attr('placeholder','该形式无需输入参与次数').attr('disabled',true);
        }
    });
    $("input[name='PromotionInfo[circle_valid]']").on("change",function () {
        var circle_valid = $(this).val();
        $('#promotioninfo-valid_circle').removeAttr('placeholder').removeAttr('disabled').val('');
        if(circle_valid == '1'){
            $('#promotioninfo-valid_circle').attr('placeholder','请输入优惠券的有效期天数');
        }else{
            $('#promotioninfo-valid_circle').attr('placeholder','该形式无需输入有效期天数').attr('disabled',true);
        }
    });
    $('#promotioninfo-style').on("change",function () {
        var style = $(this).val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        var type = $("select[name='PromotionInfo[pt_id]']").val();
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
        $('#promotioninfo-condition').removeAttr('disabled').removeAttr('placeholder').val('');
        $('#promotioninfo-discount').removeAttr('disabled').removeAttr('placeholder').val('');
        $.post(toRoute('promotion/style-change'),{
            'type':type,
            '_wine-admin':csrfToken,
            'style':style
        },function(data){
            if(data.status == '302'){
                layer.alert('登录信息已过期，请重新登录',{icon: 0},function(){
                    window.top.location.href=toRoute('site/login');
                });
                return false;
            }else if(data.status == '200'){
                var result = data.data;
                $('#promotioninfo-condition').attr(
                    'disabled',result.conditionDisable=='1').attr('placeholder',result.conditionPlaceholder).val(result.conditionValue);
                $('#promotioninfo-discount').attr(
                    'disabled',result.discountDisable=='1').attr('placeholder',result.discountPlaceholder).val(result.discountValue);
                return false;
            }else{
                layer.alert('数据出错，请重试',{icon: 0});
                return false;
            }
        },'json');
    });
});