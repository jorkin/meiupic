<?php include('head.php');?>
<div id="allpic">
    <div id="album_nav"> <span class="total_count">共 <strong><?php echo $res->get('total_num');?></strong> 个相册</span><input type="button" onclick="create_album(1)" value="创建相册" class="btn"></div>
    <ul class="album">
    <?php 
    $ls = $res->get('albums');
    if($ls):
    foreach($ls as $v):
    ?>
    <li>
        <?php if($v['private']){ ?><div class="priv" title="私有相册"></div><?php } ?>
        <span class="img">
            <a href="admin.php?ctl=album&act=photos&album=<?php echo $v['id']; ?>"><img src="<?php echo $v['cover'];?>" alt="<?php echo $v['name'];?>" /></a>
        </span>
        <span class="info"><a onclick="rename_album(this,<?php echo $v['id'];?>)"><?php echo $v['name'];?></a></span>
        <span class="control"><a href="javascript:void(0)" onclick="delete_album(this,<?php echo $v['id'];?>)"><img src="img/delete.gif" alt="删除相册" title="删除相册" /></a> <a href="javascript:void(0)" onclick="edit_priv_album(this,<?php echo $v['id'];?>)"><img src="img/lock.gif" alt="修改权限" title="修改权限" /></a></span>
        
    </li>
    <?php
    endforeach;
    endif;
    ?>
    </ul>
    <div class="pageset"><?php echo $res->get('pageset');?></div>
</div>
<?php include('foot.php');?>