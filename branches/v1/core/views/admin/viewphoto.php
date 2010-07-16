<?php include('head.php');?>

<?php $ls = $res->get('pic');?>
<?php $pre_pic = $res->get('pre_pic');?>
<?php $next_pic = $res->get('next_pic');?>
<div id="allpic">
    <div id="album_nav" class="album_detail">
        <h1 class="album_title"><?php echo $ls['name'];?></h1>
        <div class="photoinfo">
        上传时间: <?php echo date('Y-m-d H:i:s',$ls['create_time']); ?>
        <input type="button" class="btn" onclick="window.location.href='admin.php?ctl=album&act=photos&album=<?php echo $ls['album'];?>'" value="返回 <?php echo $res->get('album_name'); ?>" /></div>
    </div>
    <div id="photo-body">
         <div class="picnt">
              <img class="p-tag" src="img/pic_loading.gif">
         </div>
    </div>
    <div class="photo-right">
        <input type="button" class="btn" value="拍摄信息" onclick="show_exif(this)" /> 
        <div id="exif_info">
            <div class="top"><a href="javascript:void(0)" onclick="close_exif()">拍摄信息</a></div>
        <?php if($rs = $res->get('imgexif')){?>
            <div class="content">
                <ul>
                    <?php foreach($rs as $k=>$v):?>
                        <li><span class="exif_tit"><?php echo $k;?></span><span class="exif_val"><?php echo $v;?></span></li>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php }else{?>
        <div class="content"><div class="inf">没有EXIF信息！</div></div>
        <?php } ?>
        </div>
        
        <div id="photo_control">
            <ul>
                
                <?php if($pre_pic): ?>
                <li><a href="admin.php?ctl=photo&act=view&id=<?php echo $pre_pic['id'];?>&album=<?php echo $res->get("album");?>#photo-body"><img src="<?php echo mkImgLink($pre_pic['dir'],$pre_pic['pickey'],$pre_pic['ext'],'square');?>" /></a></li>
                <?php else:?>
                <li>这是首张</li>
                <?php endif;?>
                <li class="current"><a href="javascript:void(0)"><img src="<?php echo mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'square');?>" /></a></li>
                
                <?php if($next_pic): ?>
                <li><a href="admin.php?ctl=photo&act=view&id=<?php echo $next_pic['id'];?>&album=<?php echo $res->get("album");?>#photo-body"><img src="<?php echo mkImgLink($next_pic['dir'],$next_pic['pickey'],$next_pic['ext'],'square');?>" /></a></li>
                <?php else:?>
                <li>这是末张</li>
                <?php endif;?>
            </ul>
            <div class="prebtn"><?php if($pre_pic): ?><a class="btnpre" href="admin.php?ctl=photo&act=view&id=<?php echo $pre_pic['id'];?>&album=<?php echo $res->get("album");?>#photo-body">上一张</a><?php endif;?></div><div class="nextbtn"><?php if($next_pic): ?><a class="btnnext" href="admin.php?ctl=photo&act=view&id=<?php echo $next_pic['id'];?>&album=<?php echo $res->get("album");?>#photo-body">下一张</a><?php endif;?></div><div class="slideshow"><a href="javascript:void(0)" onclick="slideshow(<?php echo $res->get("album");?>)">幻灯片</a></div>
        </div>
        
        <div id="copyspics">
            <textarea id="copyspics_content" style="margin-left:4px;width:250px;height:150px;"></textarea><br />
            选择图片大小: <br />
            <input type="radio" name="size" value="<?php echo SITE_URL.mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'square');?>" onclick="select_copypics(this.value)" /> 方块图(75*75) <a href="<?php echo SITE_URL.mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'square');?>" target="_blank">预览</a><br />
            <input type="radio" name="size" value="<?php echo SITE_URL.mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'thumb');?>" onclick="select_copypics(this.value)" /> 缩略图(110*150) <a href="<?php echo SITE_URL.mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'thumb');?>" target="_blank">预览</a><br />
            <input type="radio" name="size" value="<?php echo SITE_URL.mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'small');?>" onclick="select_copypics(this.value)" /> 小图(240*240) <a href="<?php echo SITE_URL.mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'small');?>" target="_blank">预览</a><br />
            <input type="radio" name="size" value="<?php echo SITE_URL.mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'medium');?>" onclick="select_copypics(this.value)" checked="checked" /> 中图(550*550) <a href="<?php echo SITE_URL.mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'medium');?>" target="_blank">预览</a><br />
            <input type="radio" name="size" value="<?php echo SITE_URL.mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'big');?>" onclick="select_copypics(this.value)" /> 大图(900*900) <a href="<?php echo SITE_URL.mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'big');?>" target="_blank">预览</a><br />
            <input type="radio" name="size" value="<?php echo SITE_URL.mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'orig');?>" onclick="select_copypics(this.value)" /> 原图 <a href="<?php echo SITE_URL.mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'orig');?>" target="_blank">预览</a><br /><br />
            <input type="button" value="复制到剪切板" class="btn" id="copyspics_click" />
        </div>
    </div>
    <div class="clearfix"></div>
</div>
<?php include('foot.php');?>
<script type="text/javascript">
    function select_copypics(url){
        $("#copyspics textarea").val('<div align="center"><img src="'+url+'" /></div><br />');
    }
    
    $(function(){
        var select_url = $('#copyspics input[name=size]:checked').val();
        select_copypics(select_url);
        <?php 
        if($next_pic){
        ?>
        var imghref = 'admin.php?ctl=photo&act=view&id=<?php echo $next_pic['id'];?>&album=<?php echo $res->get("album");?>#photo-body';
        var nexttile = '点击查看下一张';
        <?php
        }else{
        ?>
        var imghref = 'javascript:void(0)';
        var nexttile = '已经是最后一张';
        <?php
        }
        ?>
        var img = new Image();
        img.onload = function(){
            var img_width = img.width;
            var max_width = $('body').width()-350;
            if (img.width > max_width) {
                img_width = max_width;
            };
            var imgload = '<div class="sh1"><div class="sh2"><div class="sh3"><a class="p-tag" hidefocus="true" href="'+imghref+'" title="'+nexttile+'"><img class="p-tag" width="'+img_width+'" src="'+img.src+'"></a></div></div></div>';
            $('#photo-body div.picnt').html(imgload);
        };
        img.src = "<?php echo mkImgLink($ls['dir'],$ls['pickey'],$ls['ext'],'big');?>";
        
        var clip = new ZeroClipboard.Client();
        clip.setText('');
        clip.setHandCursor( true );
        clip.glue('copyspics_click');
        clip.addEventListener('mouseOver',function(client) { 
            clip.setText($('#copyspics_content').val());
        });
        clip.addEventListener('complete',function(o){
            var pos = getElementOffset($('#copyspics_click').get(0));
            $('#copyedok').css('left',pos.left);
            $('#copyedok').css('top',pos.top+22);
            $('#copyedok').show().animate({opacity: 1.0}, 1000).fadeOut();
        });
        
        window.onresize = function(){
            var img_width = img.width;
            var max_width = $('body').width()-350;
            if (img.width > max_width) {
                img_width = max_width;
            };
            $('#photo-body img').attr('width',img_width);
            clip.reposition('copyspics_click');
        }

        document.onkeydown = keydown;
        function keydown(event){
        	event = event ? event : (window.event ? window.event : null); 
        	if($("#photo_control").find("a.btnpre").length > 0 && event.keyCode==37){
        		window.location=$("#photo_control").find("a.btnpre").attr("href");
        	}
        	if($("#photo_control").find("a.btnnext").length > 0 && event.keyCode==39){
        		window.location=$("#photo_control").find("a.btnnext").attr("href");
        	}
        }
    });
</script>