$(function(){
    $('.gallary_item').hover(function(){
        $(this).addClass('sel_on');
    },
    function(){
        $(this).removeClass('sel_on');
    });
    $('.inline_edit').hover(function(){
        $(this).addClass('editbg');
    },function(){
        $(this).removeClass('editbg');
    })
});

Madmin={};
Madmin.check_all = function(je,check){
    if(check){
        $(je).attr('checked','checked');
    }else{
        $(je).removeAttr('checked');
    }
}
Madmin.checked_action = function(je,action_url){
    var check_vals = $(je+':checked');
    $.post(action_url,check_vals.serializeArray(),function(data) {
        Mui.box.setData(data);
    },'html');
}
Madmin.rename = function(obj,url){
    var info = $(obj).parent();
    var id = $(obj).attr('nid');
    
    var info_txt = info.text();
    info.html('<input id="input_id_'+id+'" type="text" value="'+info_txt.replace(/\"/g, '&#34;')+'" class="inputstyle" />');
    var input = $('#input_id_'+id);
    input.focus();
    input.select();
    input.blur(
        function(){
            if(this.value != info_txt && this.value!=''){
                $.post(url,
                   {name:this.value},
                   function(data){
                        if(data.ret){
                            $(obj).html(data.html);
                            info.empty().append(obj);
                        }else{
                            info.empty().append(obj);
                        }
                    },
                'json');
            }else{
                $(obj).html(info_txt);
                info.empty().append(obj);
            }
        }
    );
    input.unbind('keypress').bind('keypress',
        function(e){
            if(e.keyCode == 13){
                input.blur();
            }
        }
    );
}

Madmin.inline_edit = function(je,url){
    var info = $(je);
    var parent = $(je).parent();
    $.get(url,{ajax:'true','_t':Math.random()}, function(data) {
        info.hide();
        if(parent.find('form').length == 0){
            parent.append(data);
        }
        $(parent).find('input[name=cancel]').click(function(){
            $(parent).find('form').remove();
            info.show();
        });
        $(parent).find('form').submit(function(){
            var postform = $(this);
            $.post(postform.attr('action'),postform.serializeArray(),function(data) {
                if(data.ret){
                    info.html(data.html+' <span class="i_editinfo sprite"></span>');
                    $(parent).find('form').remove();
                    info.show();
                }else{
                    notice_div = postform.find('.form_notice_div');
                    if( notice_div.length == 0 ){
                        postform.prepend('<div class="form_notice_div">'+data.msg+'</div>');
                    }else{
                        notice_div.html(data.msg);
                    }
                    postform.find('.form_notice_div').css({display:'block'});
                }
            },'json');
        });
    },'html');
}