$(function(){
    $('.gallary_item').hover(function(){
        $(this).addClass('sel_on');
    },
    function(){
        $(this).removeClass('sel_on');
    });
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
    info.html('<input id="input_id_'+id+'" type="text" value="'+info_txt+'" class="inputstyle" />');
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
                            $(obj).text(data.name);
                            info.empty().append(obj);
                        }else{
                            info.empty().append(obj);
                        }
                    },
                'json');
            }else{
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

