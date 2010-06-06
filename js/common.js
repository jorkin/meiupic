var userAgent = navigator.userAgent.toLowerCase();
var is_opera = userAgent.indexOf('opera') != -1 && opera.version();
var is_moz = (navigator.product == 'Gecko') && userAgent.substr(userAgent.indexOf('firefox') + 8, 3);
var is_ie = (userAgent.indexOf('msie') != -1 && !is_opera) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);
var is_mac = userAgent.indexOf('mac') != -1;

//Drag and Resize
(function($) {
    $.fn.jqDrag = function(h) {
        return i(this, h, 'd');
    };
    $.fn.jqResize = function(h) {
        return i(this, h, 'r');
    };
    $.jqDnR = {
        dnr: {},
        e: 0,
        drag: function(v) {
            if (M.k == 'd') E.css({
                left: M.X + v.pageX - M.pX,
                top: M.Y + v.pageY - M.pY
            });
            else{
                E.css({
                    width: Math.max(v.pageX - M.pX + M.W, 0),
                    height: Math.max(v.pageY - M.pY + M.H, 0)
                });
                eid = E.attr('id');
                if(eid=='floatWin'){
                    mydiv.Resize(E);
                }
            }
            return false;
        },
        stop: function() {
            E.css('opacity', M.o);
            $().unbind('mousemove', J.drag).unbind('mouseup', J.stop);
        }
    };
    var J = $.jqDnR,
    M = J.dnr,
    E = J.e,
    i = function(e, h, k) {
        return e.each(function() {
            h = (h) ? $(h, e) : e;
            h.bind('mousedown', {
                e: e,
                k: k
            },
            function(v) {
                var d = v.data,
                p = {};
                E = d.e;
                if (E.css('position') != 'relative') {
                    try {
                        E.position(p);
                    } catch(e) {}
                }
                M = {
                    X: p.left || f('left') || 0,
                    Y: p.top || f('top') || 0,
                    W: f('width') || E[0].scrollWidth || 0,
                    H: f('height') || E[0].scrollHeight || 0,
                    pX: v.pageX,
                    pY: v.pageY,
                    k: d.k,
                    o: E.css('opacity')
                };
                //E.css({
                //    opacity: 1
                //});
                $().mousemove($.jqDnR.drag).mouseup($.jqDnR.stop);
                return false;
            });
        });
    },
    f = function(k) {
        return parseInt(E.css(k)) || false;
    };
})(jQuery);
//-------------------End Drag and Resize
jQuery.fn.addOption = function(text,value){jQuery(this).get(0).options.add(new Option(text,value));}

/*-<UI*/
var mydiv = {
	Open : function(width,height,showfoot){
		obj = $('#floatwin');
		//遮盖层
		$("body").prepend('<div id="UIWinMask"><div style="position: absolute;z-index: 999;top: 0;left: 0;width: '+ $(document).width() +'px;height: '+ $(document).height() + 'px;opacity: 0.17;filter:alpha(opacity=17);background-color:#000;"><img height="100%" width="100%" src="img/place.gif" /></div></div>');
		obj.width(width);
		obj.height(height);
		if(showfoot == 1){
			$('#floatContent').height(height-25-40);
			$('#floatFoot').show();
		}else{
			$('#floatContent').height(height-25);
			$('#floatFoot').hide();
		}
		//图层对象的位置
		obj.css("left",parseInt(document.documentElement.clientWidth/2-obj.width()/2));
		obj.css("top",parseInt((document.documentElement.clientHeight-obj.height()) / 2 + document.documentElement.scrollTop));
		//显示图层
		obj.show();
	},
	Close : function(){
		//移除遮盖图层
		$("#UIWinMask").remove();
		$('#floatContent').empty();
		$('#floatFoot').empty();
		//隐藏图层
		$('#floatwin').hide();
	},
	AutoClose: function(){
		$('#UIWinMask').unbind('click').bind('click',function(){
			mydiv.Close();
		});
	}
}
/*UI>-*/

function do_create_album(){
    
    var url = 'index.php?ctl=album&act=ajax_create_album';
    var album_name=$.trim($('#floatContent').find('input[name=album_name]').val());
    
    if(album_name==''){
        alert('相册名不能为空！');
        return false;
    }
    $.post(url,
           {album_name:album_name},
           function(data){
                if(data.ret){
                    var salbum = $('#sel_album').find('select[name=albums]');
                    salbum.empty();
                    for(v in data.list){
                        salbum.addOption(data.list[v],v);
                    }
                    salbum.find("option").each(function(){
                        if($(this).text() == album_name){
                            this.selected = true;
                        }
                    });
                    mydiv.Close();
                }else{
                    alert(data.msg);
                }
            },
            'json');
}

function do_create_album_a(){
    
    var url = 'index.php?ctl=album&act=ajax_create_album';
    var album_name=$.trim($('#floatContent').find('input[name=album_name]').val());
    
    if(album_name==''){
        alert('相册名不能为空！');
        return false;
    }
    $.post(url,
           {album_name:album_name},
           function(data){
                if(data.ret){
                    mydiv.Close();
                    window.location.reload();
                }else{
                    alert(data.msg);
                }
            },
            'json');
}
function create_album(t){
    if(t==1){
        var func = 'do_create_album_a()';
    }else{
        var func = 'do_create_album()';
    }
    mydiv.Open(240,120,1);
    $('#floatwin').find('h2 span').html('创建相册');
    $('#floatFoot').html('<input type="button" value="确定" class="btn" onclick="'+func+'" /> <input type="button" value="取消" class="btn gray" onclick="mydiv.Close()" />');
	$('#floatContent').html('<div class="album_name_f"><span>相册名</span><input class="ipt_1" name="album_name" value="" /></div>');
	$('#floatContent').find('input[name=album_name]').unbind('keypress').bind('keypress',
        function(e){
            if(e.keyCode == 13){
                eval(func);
            }
        }
    );
}
function go_upload_step2(){
    var abid=$('#sel_album').find('select[name=albums]').val();
    if(abid){
        window.location.href = 'index.php?ctl=upload&act=step2&album_id='+abid;
    }else{
        alert('请先选择相册！');
    }
}

function getElementOffset(e){
	 var t = e.offsetTop;
	 var l = e.offsetLeft;
	 var w = e.offsetWidth;
	 var h = e.offsetHeight-1;

	 while(e=e.offsetParent) {
	 t+=e.offsetTop;
	 l+=e.offsetLeft;
	 }
	 return {
	 top : t,
	 left : l,
	 width : w,
	 height : h
	 }
}

function copyUrl(o){
    var imgurl = $(o).parent().parent().attr('rel');
    var input_field = $('#copytocpbord').find('input[type=text]');
    var input_btn = $('#copytocpbord').find('input[type=button]');
    var pos = getElementOffset(o);
    input_field.val(imgurl);
    
    if(pos.left+360 > document.documentElement.clientWidth){
        $('#copytocpbord').css('left',document.documentElement.clientWidth-360);
    }else{
        $('#copytocpbord').css('left',pos.left);
    }
    $('#copytocpbord').css('top',pos.top+pos.height);
    $('#copytocpbord').show();
    
    clipobj = copy_clip(imgurl,'cp_click','click_cp_button');
    $('#copytocpbord').find('input[name=close]').unbind('click').bind('click',function(){
		$('#copytocpbord').hide();
        clipobj.destroy();
	});
}

function copyCode(o){
    var imgurl = $(o).parent().parent().attr('rel');
    var input_field = $('#copytocpbord').find('input[type=text]');
    var input_btn = $('#copytocpbord').find('input[type=button]');
    var pos = getElementOffset(o);
    var code = '<div align="center"><img src="'+imgurl+'" /></div><br>';
    input_field.val(code);
    
    if(pos.left+360 > document.documentElement.clientWidth){
        $('#copytocpbord').css('left',document.documentElement.clientWidth-360);
    }else{
        $('#copytocpbord').css('left',pos.left);
    }
    $('#copytocpbord').css('top',pos.top+pos.height);
    $('#copytocpbord').show();
    clipobj = copy_clip(code,'cp_click','click_cp_button');
    $('#copytocpbord').find('input[name=close]').unbind('click').bind('click',function(){
		$('#copytocpbord').hide();
        clipobj.destroy();
	});
}

function click_cp_button(){
    var pos = getElementOffset($('#cp_click').get(0));
    $('#copyedok').css('left',pos.left);
    $('#copyedok').css('top',pos.top);
    $('#copyedok').show().animate({opacity: 1.0}, 1000).fadeOut();
    $('#copytocpbord').animate({opacity: 1.0}, 1000).fadeOut();
}
function copy_clip(copy,bid,func){
	var clip = new ZeroClipboard.Client();
	clip.setText('');
	clip.setHandCursor( true );
	clip.addEventListener('mouseOver',function(client) { 
		clip.setText(copy);
	});

	clip.addEventListener('complete',function(o){
	    clip.destroy();
		eval(func+'();');
	})
	clip.glue(bid);
	return clip;
}

function delete_pic(o,id){
    if(confirm('确定要删除吗?')){
        var url = 'index.php?ctl=album&act=ajax_delphoto&id='+id;
        $.post(url,
               {},
               function(data){
                    if(data.ret){
                        $(o).parent().parent().animate( {opacity: 0.2} , 200)
                                              .animate( {opacity: 1} , 200)
                                              .fadeOut();
                        var pic_count = $('#album_nav').find('span.total_count strong');
                        pic_count.text(parseInt(pic_count.text())-1)
                    }else{
                        alert(data.msg);
                    }
                },
                'json');
    }else{
        return false;
    }
}
function rename_pic(o,id){
    var info = $(o).parent();
    var info_txt = info.text();
    info.html('<input id="input_id_'+id+'" type="text" value="'+info_txt+'" class="ipt_2" />');
    var input = $('#input_id_'+id);
    input.focus();
    input.select();
    input.blur(
        function(){
                var url = 'index.php?ctl=album&act=ajax_renamephoto&id='+id;
                $.post(url,
                   {name:this.value},
                   function(data){
                        if(data.ret){
                            info.html('<a onclick="rename_pic(this,'+id+')">'+data.picname+'</a>');
                        }else{
                            alert(data.msg);
                            info.html('<a onclick="rename_pic(this,'+id+')">'+info_txt+'</a>');
                        }
                    },
                'json');
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

function delete_album(o,id){
    if(confirm('确定要删除相册吗?删除相册将会删除相册内所有的图片！')){
        var url = 'index.php?ctl=album&act=ajax_delalbum&id='+id;
        $.post(url,
               {},
               function(data){
                    if(data.ret){
                        $(o).parent().parent().animate( {opacity: 0.2} , 200)
                                              .animate( {opacity: 1} , 200)
                                              .fadeOut();
                        var pic_count = $('#album_nav').find('span.total_count strong');
                        pic_count.text(parseInt(pic_count.text())-1)
                    }else{
                        alert(data.msg);
                    }
                },
                'json');
    }else{
        return false;
    }
}
function rename_album(o,id){
    var info = $(o).parent();
    var info_txt = info.text();
    info.html('<input id="input_id_'+id+'" type="text" value="'+info_txt+'" class="ipt_2" />');
    var input = $('#input_id_'+id);
    input.focus();
    input.select();
    input.blur(
        function(){
                var url = 'index.php?ctl=album&act=ajax_renamealbum&id='+id;
                $.post(url,
                   {name:this.value},
                   function(data){
                        if(data.ret){
                            info.html('<a onclick="rename_album(this,'+id+')">'+data.albumname+'</a>');
                        }else{
                            alert(data.msg);
                            info.html('<a onclick="rename_album(this,'+id+')">'+info_txt+'</a>');
                        }
                    },
                'json');
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

function set_pic_cover(o,id){
    var url = 'index.php?ctl=album&act=ajax_set_cover&id='+id;
    $.post(url,
       {name:this.value},
       function(data){
            if(data.ret){
                var pos = getElementOffset($(o).parent().parent().find('span.img').get(0));
                $('#info_msg').css('left',pos.left);
                $('#info_msg').css('top',pos.top);
                $('#info_msg').text('已成功设为封面！');
                $('#info_msg').show().animate({opacity: 1.0}, 1000).fadeOut();
            }else{
                alert(data.msg);
            }
        },
    'json');
}

function move_pic_to(type,o,id){
    var url = 'index.php?ctl=album&act=ajax_get_albums';
    var pos = getElementOffset(o);
    
    $.post(url,
       {id:id},
       function(data){
            if(data.ret){
                var salbum = $('#movetoalbum').find('select[name=albums]');
                salbum.empty();
                for(v in data.list){
                    salbum.addOption(data.list[v],v);
                }
                
                if(pos.left+360 > document.documentElement.clientWidth){
                    $('#movetoalbum').css('left',document.documentElement.clientWidth-360);
                }else{
                    $('#movetoalbum').css('left',pos.left);
                }
                $('#movetoalbum').css('top',pos.top+pos.height);
                $('#movetoalbum').show();
                $('#movetoalbum').find('input[name=move]').unbind('click').bind('click',function(){
            		var al_id = salbum.val();
            		do_move_pic_to(type,o,al_id,id);
            	});
                $('#movetoalbum').find('input[name=close]').unbind('click').bind('click',function(){
            		$('#movetoalbum').hide();
            	});
            }else{
                alert(data.msg);
            }
        },
    'json');    
}

function do_move_pic_to(type,o,al_id,id){
    var url = 'index.php?ctl=album&act=ajax_move_to_albums';
    $.post(url,
       {album_id:al_id,id:id},
       function(data){
            if(data.ret){
                $('#movetoalbum').hide();
                if(type == 2){
                    $(o).parent().parent().animate( {opacity: 0.2} , 200)
                                          .animate( {opacity: 1} , 200)
                                          .fadeOut();
                    var pic_count = $('#album_nav').find('span.total_count strong');
                    pic_count.text(parseInt(pic_count.text())-1);
                }else{
                    var pos = getElementOffset($(o).parent().parent().find('span.img').get(0));
                    $('#info_msg').css('left',pos.left);
                    $('#info_msg').css('top',pos.top);
                    $('#info_msg').text('已成功修改！');
                    $('#info_msg').show().animate({opacity: 1.0}, 1000).fadeOut();
                }
            }else{
                alert(data.msg);
            }
        },
    'json');
}

function reupload_pic(o,id){
    var pos = getElementOffset(o);
    
    if(pos.left+360 > document.documentElement.clientWidth){
        $('#reuploadpic').css('left',document.documentElement.clientWidth-360);
    }else{
        $('#reuploadpic').css('left',pos.left);
    }
    $('#reuploadpic').css('top',pos.top+pos.height);
    $('#reuploadpic').show();
    $('#reuploadpic').find('span.upfield').html('<input type="file" name="imgs">');
    $('#reuploadpic').find('form').attr('action','index.php?ctl=upload&act=reupload&id='+id);
    $('#reuploadpic').find('form').submit(function(){
        $('#reuploadpic').find('div.uploading').width($('#reuploadpic').find('form').width()).show();
    });
    $('#reuploadpic').find('input[name=close]').unbind('click').bind('click',function(){
        $('#reuploadpic').hide();
    });
}
function reupload_alert(err){
    alert(err);
    $('#reuploadpic').find('div.uploading').hide();
}
function reupload_ok(id,big,thumb){
    var rand = Math.random();
    $('#reuploadpic').find('div.uploading').hide();
    $('#reuploadpic').hide();
    $('#i_'+id).find('span.img img').attr('src',thumb+'#rand='+rand);
}
function switch_div(o,d){
    if(o.checked){
        $("#"+d).show();
    }else{
        $("#"+d).hide();
    }
}
function check_form(o){
    if(o.oldpass.value==''){
        alert('请输入原密码！');
        return false;
    }
    
    if(o.newpass.value==''){
        alert('请输入新密码！');
        return false;
    }
    
    if(o.newpass.value!=o.passagain.value){
        alert('两次密码输入不一致！');
        return false;
    }
    
    o.submit();
}

function slideshow(id){
	$("body").prepend('<div id="slide_show" style="width: '+ $(document).width() +'px;height: '+ $(document).height() + 'px;"></div><div id="slide_show_flash"></div>');
	var flashvars = {};
	flashvars.galleryURL = escape("index.php?ctl=photo&act=gallery&album="+id);
	var params = {};
	params.allowfullscreen = true;
	params.wmode = 'transparent';
	params.allowscriptaccess = "always";
	params.bgcolor = "222222";
	swfobject.embedSWF("js/simpleviewer.swf", "slide_show_flash", "100%", "100%", "9.0.124", false, flashvars, params);
	$("body").prepend('<div id="slide_show_close" onclick="close_slideshow()" title="关闭"></div>');
}

function close_slideshow(){
	$("#slide_show").remove();
	$("#slide_show_close").remove();
	$("#slide_show_flash").remove();
}

function select_pic(o){
	var select_div = $(o).parent().parent().find('div.selected');
	if(o.checked){
		select_div.show();
	}else{
		select_div.hide();
	}
}

function checkall(form){
	$('form[name='+form+']').find('input[type=checkbox]').each(function(i){
	    this.checked = true;
	    $(this).parent().parent().find('div.selected').show();
	})
}

function uncheckall(form){
    $('form[name='+form+']').find('input[type=checkbox]').each(function(i){
	    this.checked = false;
	    $(this).parent().parent().find('div.selected').hide();
	})
}
function submit_bat(form){
    var selected_ids=0
	$(form).find("input[name='picture\[\]']:checked").each(
		function(i){
			selected_ids++;
		}
	)
	if(selected_ids==0){
	    alert('请先选择图片！');
	    return false;
	}
    var v = $(form).find('select[name=do_action]').val();
    if(v=='delete' && confirm('确定要删除这些记录吗?')){
		form.submit();
	}else if(v=='move' && confirm('确定要移动这些图片?')){
	    form.submit();
	}
}
function init_submit_bat(){
    $('#batch_ctrl select[name=do_action]').find('option').each(function(i){
        if(this.value=='-1'){
            this.selected = true;
        }else{
            this.selected = false;
        }
    })
    $('#batch_ctrl select[name=albums]').find('option').each(function(i){
        if(this.value=='-1'){
            this.selected = true;
        }else{
            this.selected = false;
        }
    })
    $('#batch_ctrl select[name=do_action]').change(function(){
    	var v = $('#batch_ctrl select[name=do_action] option:selected').val();
    	if(v == -1){
    	    $('#batch_ctrl select[name=albums]').hide();
    	    $('#batch_ctrl input[name=do_pic_act]').attr('disabled',true);
    	    return 
	    }

    	if(v=='delete'){
    		$('#batch_ctrl select[name=albums]').hide();
    		$('#batch_ctrl input[name=do_pic_act]').attr('disabled',false);
    	}else if(v=='move'){
    	    $('#batch_ctrl select[name=albums]').show();
    	    $('#batch_ctrl input[name=do_pic_act]').attr('disabled',true);
    	}else{
    		$('#batch_ctrl select[name=albums]').hide();
    	}
    });

    $('#batch_ctrl select[name=albums]').change(function(){
    	var v = $('#batch_ctrl select[name=albums] option:selected').val();
    	if(v == -1) 
    	    return $('#batch_ctrl input[name=do_pic_act]').attr('disabled',true);
    	$('#batch_ctrl input[name=do_pic_act]').attr('disabled',false);
    });
}

function show_exif(o){
    var pos = getElementOffset(o);
    
    $('#exif_info').css({'left':pos.left,'top':pos.top-2});
    $('#exif_info').show();
    $(document).bind("mousedown",function(e){
        var popup = $('#exif_info').get(0);
        var e = e || window.event;
        var target = e.target || e.srcElement;
        while (target != document && target != popup) {
            target = target.parentNode;
        }
        if (target == document) {
            close_exif();
        }
    });
}
function close_exif(){
    $('#exif_info').hide();
    $(document).unbind("mousedown");
}

$(function(){
	$('.dragable').jqDrag('.draghandle');
	$('ul.album').find('li').each(function(i){
	    this.onmouseover=function(){
	        $('ul.album').find('li').removeClass('hover');
	        $(this).addClass('hover');
	    }

		if($(this).find('input[type=checkbox]').attr('checked')){
			$(this).find('div.selected').show();
		}
	});
});