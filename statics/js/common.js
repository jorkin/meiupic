/*!
 * MeiuPic common js v2.0
 * http://meiu.cn/
 *
 * Copyright 2011, Lingter
 */
 
/*drag and drop start*/
(function($){
$.fn.jqDrag=function(h){return i(this,h,'d');};
$.fn.jqResize=function(h){return i(this,h,'r');};
$.jqDnR={dnr:{},e:0,
drag:function(v){
 if(M.k == 'd')E.css({left:M.X+v.pageX-M.pX,top:M.Y+v.pageY-M.pY});
 else E.css({width:Math.max(v.pageX-M.pX+M.W,0),height:Math.max(v.pageY-M.pY+M.H,0)});
  return false;},
stop:function(){E.css('opacity',M.o);$(document).unbind('mousemove',J.drag).unbind('mouseup',J.stop);}
};
var J=$.jqDnR,M=J.dnr,E=J.e,
i=function(e,h,k){return e.each(function(){h=(h)?$(h,e):e;
 h.bind('mousedown',{e:e,k:k},function(v){var d=v.data,p={};E=d.e;
 // attempt utilization of dimensions plugin to fix IE issues
 if(E.css('position') != 'relative'){try{E.position(p);}catch(e){}}
 M={X:p.left||f('left')||0,Y:p.top||f('top')||0,W:f('width')||E[0].scrollWidth||0,H:f('height')||E[0].scrollHeight||0,pX:v.pageX,pY:v.pageY,k:d.k,o:E.css('opacity')};
 E.css({opacity:0.8});$(document).mousemove($.jqDnR.drag).mouseup($.jqDnR.stop);
 return false;
 });
});},
f=function(k){return parseInt(E.css(k))||false;};
})(jQuery);
/*drag and drop end*/
/*jquery plugin addOption*/
jQuery.fn.addOption = function(text,value){jQuery(this).get(0).options.add(new Option(text,value));}

/*jquery plugin cookie*/
jQuery.cookie = function (key, value, options) {
    if (arguments.length > 1 && String(value) !== "[object Object]") {
        options = jQuery.extend({}, options);
        if (value === null || value === undefined) {
            options.expires = -1;
        }
        if (typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }
        value = String(value);
        return (document.cookie = [
            encodeURIComponent(key), '=',
            options.raw ? value : encodeURIComponent(value),
            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
            options.path ? '; path=' + options.path : '',
            options.domain ? '; domain=' + options.domain : '',
            options.secure ? '; secure' : ''
        ].join(''));
    }
    options = value || {};
    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};

var Mui = {
    centerMe : function(jel){
        var w_w = $(jel).outerWidth();
        var w_h = $(jel).outerHeight();
        var left = $(window).scrollLeft() + (($(window).width()-w_w)/2);
        if($(jel).css('position') == 'fixed'){
            var top = ((document.documentElement.clientHeight-w_h)/2) - 50;
        }else{
            var top = $(window).scrollTop() + ((document.documentElement.clientHeight-w_h)/2) - 50;
        }
        if( top < 100 ) top = 100;
        $(jel).css({'left':left});
        $(jel).css({'top':top});
    }
};

Mui.box = {
    callback : null,
    show : function(url){
        if( $('#meiu_float_box').length == 0 ){
            $('body').prepend('<div id="meiu_float_box"></div>');
        }
        if(jQuery.browser.msie && jQuery.browser.version < 7){
            $('body').append('<iframe class="bg_iframe" scrolling="no" frameborder="0" style="position: absolute;"></iframe>');
        }
        if(url){
            $.get(url,{ajax:'true','_t':Math.random()}, function(data) {
                $('#meiu_float_box').html(data);
                Mui.centerMe('#meiu_float_box');
                $('#meiu_float_box').jqDrag('.box_title');
            });
            $('#meiu_float_box').html('<div class="loading">Loading...</div>');
            $('#meiu_float_box').show();
            Mui.centerMe('#meiu_float_box');
        }else{
            $('#meiu_float_box').show();
            Mui.centerMe('#meiu_float_box');
        }
    },
    setData : function(data){
        if( $('#meiu_float_box').length == 0 ){
            $('body').prepend('<div id="meiu_float_box"></div>');
        }
        $('#meiu_float_box').html(data);
        $('#meiu_float_box').show();
        Mui.centerMe('#meiu_float_box');
    },
    resize: function(w,h){
        $('#meiu_float_box').width(w);
        if(h){
            $('#meiu_float_box').height(h);
        }
    },
    close : function(){
        $('#meiu_float_box').hide();
        if(jQuery.browser.msie && jQuery.browser.version < 7){
            $('iframe').remove('.bg_iframe');
        }
        this.callback = null;
    },
    alert: function(title,content,btn_name){
        Mui.box.setData('<div class="box_title titbg">'+
            '<div class="closer sprite i_close" onclick="Mui.box.close()"></div>'+
            title+'</div><div class="box_container">'+'<div>'+content+'</div>'+
            '<div class="b_btns"><input type="button" value="'+btn_name+'" class="ml10 ylbtn f_left" name="cancel" onclick="Mui.box.close()"></div></div>');
        $('#meiu_float_box').jqDrag('.box_title');
    }
};

Mui.form = {
    send : function(formid){
        $('#'+formid).unbind('submit').submit(function(){
            $.post($('#'+formid).attr('action'),$('#'+formid).serializeArray(),function(data) {
                Mui.form.showResult(data,formid);
            },'html');
        });
    },
    sendPop : function(formid){
        $('#'+formid).unbind('submit').submit(function(){
            $.post($('#'+formid).attr('action'),$('#'+formid).serializeArray(),function(data) {
                Mui.box.setData(data);
            },'html');
        });
    },
    sendAuto : function(formid){
        $('#'+formid).unbind('submit').submit(function(){
            $.post($('#'+formid).attr('action'),$('#'+formid).serializeArray(),function(data) {
                if(data.ret){
                    if(Mui.box.callback){
                        Mui.box.setData(data.html.replace(/<script(.|\s)*?\/script(\s)*>/gi,"") );
                        Mui.box.callback();
                    }else{
                        Mui.box.setData(data.html);
                    }
                }else{
                    Mui.form.showResult(data.html,formid);
                }
            },'json');
        });
    },
    showResult : function(ret,formid){
        if( ret != '' ){
            if( $('#meiu_notice_div').length == 0 && formid != '' ){
                $('#'+formid).before('<div id="meiu_notice_div"></div>');
                $('#meiu_notice_div').html(ret);
            }else{
                $('#meiu_notice_div').html(ret);
            }
            $('#meiu_notice_div').css({display:'block'});
        }else{
            if( $('#meiu_notice_div').length > 0 ){
                $('#meiu_notice_div').css({display:'none'});
            }
        }
    }
};

function drop_select(je){
    $(je).find('li').each(function(i){
        if($(this).hasClass('current')){
            $(je).find('.selected').append($(this).html());
            $(je).find('.selected').prepend('<div class="arrow sprite"></div>');
        }
    });
    $(je).hover(function(){
        $(je).find('.optlist').show();
        },function(){
        $(je).find('.optlist').hide();
    });
}

function setMask(id,state){
    var oldEl = $('#'+id);
    if(oldEl.length == 0){
        return;
    }
    var val=oldEl.val();
    var cla=oldEl.attr('class');
    var name=oldEl.attr('name');
    var sibling = oldEl.next();
    var newInput = document.createElement('input');
    
    $(newInput).val(val);
    $(newInput).attr('id',id);
    $(newInput).attr('class',cla);
    $(newInput).attr('name',name);
    if (state == true)
        $(newInput).attr('type','text');
    else
        $(newInput).attr('type','password');
    
    oldEl.remove();
    sibling.before($(newInput));
}

function page_setting(t,num){
    var cookie_name = '_pageset_'+t;
    $.cookie(cookie_name,num,{expires: 7, path: '/'});
    window.location.reload();
}

function sort_setting(t,sort){
    var cookie_name = '_sortset_'+t;
    $.cookie(cookie_name,sort,{expires: 7, path: '/'});
    window.location.reload();
}

function reply_comment(je,url){
    var btn = $(je);
    var parent = $(je).parent();
    $.get(url,{ajax:'true','_t':Math.random()}, function(data) {
        if(parent.find('form').length == 0){
            parent.append(data);
        }
        $(parent).find('input[name=cancel]').click(function(){
            $(parent).find('form').remove();
        });
        $(parent).find('form').submit(function(){
            var postform = $(this);
            $.post(postform.attr('action'),postform.serializeArray(),function(data) {
                if(data.ret){
                    var reply_p = postform.parent().parent().parent();
                    if(reply_p.hasClass('sub')){
                        reply_p.after(data.html);
                    }else{
                        postform.parent().after(data.html);
                    }
                    
                    postform.remove();
                }else{
                    notice_div = postform.find('.form_notice_div');
                    if( notice_div.length == 0 ){
                        postform.prepend('<div class="form_notice_div">'+data.html+'</div>');
                    }else{
                        notice_div.html(data.html);
                    }
                    postform.find('.form_notice_div').css({display:'block'});
                }
            },'json');
        });
    },'html');
}

function reload_comments(url){
    $.get(url,{ajax:'true','_t':Math.random()}, function(data) {
        if(data){
            setTimeout(function(){
                Mui.box.close();
            },500);
            $('.comment_list').html(data);
        }
    },'html');
}

function load_comments(url){
    $('.more_comments').html('Loading...');
    
    $.get(url,{ajax:'true','_t':Math.random()}, function(data) {
        if(data){
            $('.more_comments').remove();
            $('.comment_list').append(data);
        }
    },'html');
}

$(function(){
    //press esc to close float div
    $(window).bind('keypress',
        function(e){
            if(e.keyCode == 27){
                Mui.box.close();
            }
        }
    );
});