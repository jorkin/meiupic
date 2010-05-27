<?php include('head.php');?>
<div id="allpic">
    <div id="album_nav"> <span class="total_count">共 <strong><?php echo $res->get('total_num');?></strong> 张图片</span> <input type="button" class="btn" value="我要上传图片" onclick="window.location.href='index.php?ctl=upload'" /></div>
    <ul class="album highslide-gallery">
    <?php 
    $ls = $res->get('pics');
    if($ls):
    foreach($ls as $v):
    ?>
    <li id="i_<?php echo $v['id'];?>" rel="<?php echo SITE_URL.imgSrc($v['path']); ?>">
        <span class="img">
            <a  class="highslide" href="<?php echo imgSrc($v['path']); ?>" target="_blank" onclick="return hs.expand(this)"><img src="<?php echo imgSrc($v['thumb']); ?>" alt="<?php echo $v['name'];?>" /></a>
        </span>
        <span class="info"><a onclick="rename_pic(this,<?php echo $v['id'];?>)"><?php echo $v['name'];?></a></span>
        <span class="control">
            <a href="javascript:void(0)" onclick="copyUrl(this)"><img src="img/copyu.gif" alt="复制网址" title="复制网址" /></a> 
            <a href="javascript:void(0)" onclick="copyCode(this)"><img src="img/copyc.gif" alt="复制代码" title="复制代码" /></a> 
            <a href="javascript:void(0)" onclick="delete_pic(this,<?php echo $v['id'];?>)"><img src="img/delete.gif" alt="删除" title="删除" /></a> 
            <a href="javascript:void(0)" onclick="reupload_pic(this,<?php echo $v['id'];?>)"><img src="img/re_upload.gif" alt="重新上传" title="重新上传此图片" /></a> 
            <a href="javascript:void(0)" onclick="move_pic_to(1,this,<?php echo $v['id'];?>)"><img src="img/moveto.gif" alt="移动到相册" title="移动到相册" /></a>
        </span>
    </li>
    <?php
    endforeach;
    else:
        echo "<li>无图片</li>";
    endif;
    ?>
    </ul>
    <div class="pageset"><?php echo $res->get('pageset');?></div>
</div>
<?php include('foot.php');?>