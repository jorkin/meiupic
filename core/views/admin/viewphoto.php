<?php include('head.php');?>

<?php $ls = $res->get('pic');?>
<?php $pre_pic = $res->get('pre_pic');?>
<?php $next_pic = $res->get('next_pic');?>
<div id="allpic">
    <div id="album_nav" class="album_detail">
        <h1 class="album_title"><?php echo $ls['name'];?></h1>
        <div class="photoinfo"><input type="button" class="btn" value="拍摄信息" onclick="show_exif(this)" /> <input type="button" class="btn bt2" onclick="window.location.href='index.php?ctl=album&act=photos&album=<?php echo $ls['album'];?>'" value="返回 <?php echo $res->get('album_name'); ?>" /></div>
    </div>
    <div id="photo-body">
         <div class="picnt">
              <img class="p-tag" src="img/pic_loading.gif"></a>   
         </div>
    </div>
    <div class="photo-right">
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
                <li><a href="index.php?ctl=photo&act=view&id=<?php echo $pre_pic['id'];?>&album=<?php echo $res->get("album");?>#photo-body"><img src="<?php echo imgSrc($pre_pic['thumb']);?>" /></a></li>
                <?php else:?>
                <li>这是首张</li>
                <?php endif;?>
                <li class="current"><a href="javascript:void(0)"><img src="<?php echo imgSrc($ls['thumb']);?>" /></a></li>
                
                <?php if($next_pic): ?>
                <li><a href="index.php?ctl=photo&act=view&id=<?php echo $next_pic['id'];?>&album=<?php echo $res->get("album");?>#photo-body"><img src="<?php echo imgSrc($next_pic['thumb']);?>" /></a></li>
                <?php else:?>
                <li>这是末张</li>
                <?php endif;?>
            </ul>
            <div class="prebtn"><?php if($pre_pic): ?><a href="index.php?ctl=photo&act=view&id=<?php echo $pre_pic['id'];?>&album=<?php echo $res->get("album");?>#photo-body">上一张</a><?php endif;?></div><div class="nextbtn"><?php if($next_pic): ?><a href="index.php?ctl=photo&act=view&id=<?php echo $next_pic['id'];?>&album=<?php echo $res->get("album");?>#photo-body">下一张</a><?php endif;?></div><div class="slideshow"><a href="javascript:void(0)" onclick="slideshow(<?php echo $res->get("album");?>)">幻灯片</a></div>
        </div>
    </div>
    <div class="clearfix"></div>
    <script>
    (function(){
        <?php 
        if($next_pic){
        ?>
        var imghref = 'index.php?ctl=photo&act=view&id=<?php echo $next_pic['id'];?>&album=<?php echo $res->get("album");?>#photo-body';
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
        img.src = "index.php?ctl=photo&act=resize&size=big&id=<?php echo $ls['id'];?>";
        img.onload = function(){
            var imgload = '<div class="sh1"><div class="sh2"><div class="sh3"><a class="p-tag" hidefocus="true" href="'+imghref+'" title="'+nexttile+'"><img class="p-tag" src="'+img.src+'"></a></div></div></div>';
            $('#photo-body div.picnt').html(imgload);
        }
    })();
    </script>
</div>
<?php include('foot.php');?>