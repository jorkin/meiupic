<?php include('head.php');?>
<div class="main box1">
    <div class="bg1 title"><h3 id="album_ptitle"><?php echo $res->get('album_name'); ?> &gt; <span></span></h3></div>
    <div class="box_body">
        <div class="photo_control">
            <button class="nav_pre"  onfocus="this.blur()" onclick="showPre()">
                <span>上一张</span>
            </button>
            <div id="miniphoto_list">
                <ul class="thumblist" style="width:<?php echo $res->get('mini_photo_width');?>px;">
                    <?php $ls = $res->get('piclist');
                    if($ls):
                        foreach($ls as $k=>$v):
                    ?>
                    <li id="li_<?php echo $k;?>" rel="<?php echo $v['id'];?>">
                        <div class="photo">
                        <a href="javascript:void(0)" onclick="return showPhoto(<?php echo $k;?>);" onfocus="this.blur()"><img id="i_<?php echo $k;?>" width="50" height="50" title="<?php echo $v['name']; ?>" rel="<?php echo mkImgLink($v['dir'],$v['pickey'],$v['ext'],'big');?>" src="about:blank" csrc="<?php echo mkImgLink($v['dir'],$v['pickey'],$v['ext'],'square');?>" /></a>
                        </div>
                    </li>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </ul>
            </div>
            <button class="nav_next" onfocus="this.blur()" onclick="showNext()">
                <span>下一张</span>
            </button>
        </div>
        <div id="show_photo_page">第 <span class="cur">0</span> 张 / 共 <?php echo $res->get('total_imgs'); ?> 张</div>
        <div id="photo_content">
            <span id="imgarea"></span>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php include('foot.php');?>
<script type="text/javascript" charset="utf-8">
    var SmallController = new Object();
    SmallController.current = 0;
    SmallController.leftld = 0;
    SmallController.rightld = 0;

    function getHash(k){
        var hash = window.location.hash;
        if(!hash)
            return false;
        hash_string = hash.substring(1);
        hash_arr = hash_string.split('&');
        for(i in hash_arr){
            k_v = hash_arr[i].split('=');
            if(k_v[0] == k){
                return k_v[1];
            }
        }
    }
    
    function showSmall(){
       for(var i=SmallController.current-SmallController.leftld;i<=SmallController.current+SmallController.rightld;i++){
           if($('#i_'+i).length > 0 && $('#i_'+i).attr('csrc')){
               $('#i_'+i).attr('src',$('#i_'+i).attr('csrc'));
               $('#i_'+i).removeAttr('csrc');
           }
       }
    }
    function showPre(){
        if($('#miniphoto_list li.current').length > 0){
            var no_id = $('#miniphoto_list li.current').attr('id').split('_');
            if(parseInt(no_id[1])>0){
                showPhoto(parseInt(no_id[1])-1);
            }else{
                showPhoto($('#miniphoto_list li').length-1);
            }
        }
    }
    function showNext(){
        if($('#miniphoto_list li.current').length > 0){
            var no_id = $('#miniphoto_list li.current').attr('id').split('_');
            var total = $('#miniphoto_list li').length;
            if(parseInt(no_id[1])+1<total){
                showPhoto(parseInt(no_id[1])+1);
            }else{
                showPhoto(0);
            }
        }
    }
    
    function showPhoto(v) {
        if($('#i_'+v).length == 0){
            return false;
        }
        var img_title = $('#i_'+v).attr('title');
        var img_src = $('#i_'+v).attr('rel');
        $('#imgarea').html('<img class="p-tag" src="img/pic_loading.gif">');
        var img = new Image();
        img.src = img_src;
        img.onload = function(){
            if(img.width > 750){
                img_width = 750;
            }else{
                img_width = img.width;
            }
            $('#miniphoto_list li').removeClass('current');
            $('#li_'+v).addClass('current');
            $('#album_ptitle span').text(img_title);
            $('#imgarea').html('<img src="'+img_src+'" width="'+img_width+'" />');
        }
        var miniphoto_left = $('#miniphoto_list').get(0).scrollLeft;
        var total_width = $('#miniphoto_list ul.thumblist').width();
        
        SmallController.current = v;
        
        if(parseInt(v)*65 <= 0){
            $('#miniphoto_list').animate({ scrollLeft : 0}, 'fast');
            SmallController.leftld = 0;
            SmallController.rightld = 10;
            showSmall();
        }else if((parseInt(v)+1)*65 >= total_width){
            $('#miniphoto_list').animate({ scrollLeft : total_width-11*65 }, 'fast');
            SmallController.leftld = 10;
            SmallController.rightld = 0;
            showSmall();
        }else if(parseInt(v)*65 > miniphoto_left+65*9){
            $('#miniphoto_list').animate({ scrollLeft : parseInt(v-5)*65 }, 'fast');
            SmallController.leftld = 5;
            SmallController.rightld = 5;
            showSmall();
        }else if(parseInt(v)*65 < miniphoto_left+65){
            $('#miniphoto_list').animate({ scrollLeft : parseInt(v-5)*65 }, 'fast');
            SmallController.leftld = 5;
            SmallController.rightld = 5;
            showSmall();
        }
        $('#show_photo_page span.cur').text(parseInt(v)+1);
        window.location.hash = '#photo='+$('#li_'+v).attr('rel');
    }
    $(function(){
        var photo_id = getHash('photo');
        
        if(photo_id){
            if($('#miniphoto_list li[rel="'+photo_id+'"]').length > 0){
                var no_id = $('#miniphoto_list li[rel="'+photo_id+'"]').attr('id').split('_');
                showPhoto(no_id[1]);
            }else{
                showPhoto(0);
            }
            
        }else{
            showPhoto(0);
        }
        
        document.onkeydown = keydown;
        function keydown(event){
            event = event ? event : (window.event ? window.event : null); 
            if(event.keyCode==37){
                showPre();
            }
            if(event.keyCode==39){
                showNext();
            }
        }
        
        $('#imgarea').mousemove(function(e){
            var ps = $('#imgarea').offset();
            var ps_width = $('#imgarea').width();
            var nxt = e.clientX > (ps.left+ps_width/2);
            var curClass = nxt ? 'next_cur' : 'pre_cur';
            $('#imgarea').attr('class',curClass);
            $('#imgarea img').attr('title','点击跳到' + (nxt ? '下一张' : '上一张'));
        });
        
        $('#imgarea').click(function(e){
            var ps = $('#imgarea').offset();
            var ps_width = $('#imgarea').width();
            var nxt = e.clientX > (ps.left+ps_width/2);
            if(nxt){
                showNext();
            }else{
                showPre();
            }
        });
    })
</script>