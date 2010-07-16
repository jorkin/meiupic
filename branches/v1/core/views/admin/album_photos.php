<?php include('head.php');?>
<div id="allpic">
    <div id="album_nav" class="album_detail">
        <h1 class="album_title"><?php echo $res->get('album_name');?></h1>
        <span class="total_count">共 <strong><?php echo $res->get('total_num');?></strong> 张图片</span> <input type="button" class="btn" value="上传图片" onclick="window.location.href='admin.php?ctl=upload&act=step2&album_id=<?php echo $res->get('album');?>'" />
    </div>
    <form name="pics_form" action="admin.php?ctl=photo&act=bat&album=<?php echo $res->get('album');?>&referf=album&referp=<?php echo $res->get('page');?>" method="post" onsubmit="submit_bat(this);return false;">
    <div id="batch_ctrl"> 
        <input type="button" class="btn" value="全选" onclick="checkall('pics_form')" />  
        <input type="button" class="btn" value="取消全选" onclick="uncheckall('pics_form')" /> 
        <select name="do_action">
            <option value="-1">批量操作</option>
            <option value="delete">选中图片彻底删除</option>
            <option value="move">选中图片移动到</option>
        </select>
        <select name="albums" style="display:none">
            <option value="-1">选择相册</option>
        <?php 
            $ls = $res->get('albums_list');
            if($ls){
                foreach($ls as $k=>$v){
                    echo "<option value=\"".$k."\">".$v."</option>\n";
                }
            }
        ?>
        </select>
         <input name="do_pic_act" type="submit" value="执行" class="btn" disabled />
         <select name="do_sort" onchange="change_order(this.value);">
              <option value="-1">选择排序</option>
              <option value="admin.php?ctl=album&act=photos&album=<?php echo $res->get('album');?>&sort=time_desc&page=<?php echo $res->get('page');?>" <?php if($res->get('sort')=='time_desc'){ echo 'selected="selected"';} ?>>按照时间从近到远排序</option>
              <option value="admin.php?ctl=album&act=photos&album=<?php echo $res->get('album');?>&sort=time_asc&page=<?php echo $res->get('page');?>" <?php if($res->get('sort')=='time_asc'){ echo 'selected="selected"';} ?>>按照时间从远到近排序</option>
          </select>
         <input type="button" value="幻灯片查看" class="btn" onclick="slideshow(<?php echo $res->get('album');?>)" />
    </div>
    <?php if($res->get('msginfo')){ echo $res->get('msginfo');}?>
    <ul class="album">
    <?php 
    $ls = $res->get('pics');
    if($ls):
    foreach($ls as $v):
    ?>
    <li id="i_<?php echo $v['id'];?>" rel="<?php echo SITE_URL.mkImgLink($v['dir'],$v['pickey'],$v['ext'],'orig');?>">
        <span class="img">
            <a href="admin.php?ctl=photo&act=view&id=<?php echo $v['id'];?>&album=<?php echo $res->get('album');?>">
                <img src="<?php echo mkImgLink($v['dir'],$v['pickey'],$v['ext'],'thumb');?>" source="<?php echo mkImgLink($v['dir'],$v['pickey'],$v['ext'],'orig');?>" alt="<?php echo $v['name'];?>" />
            </a>
        </span>
        <span class="info"><a onclick="rename_pic(this,<?php echo $v['id'];?>)"><?php echo $v['name'];?></a></span>
        <span class="control">
            <a href="javascript:void(0)" onclick="copyUrl(this)"><img src="img/copyu.gif" alt="复制网址" title="复制网址" /></a> 
            <a href="javascript:void(0)" onclick="copyCode(this)"><img src="img/copyc.gif" alt="复制代码" title="复制代码" /></a> 
            <a href="javascript:void(0)" onclick="delete_pic(this,<?php echo $v['id'];?>)"><img src="img/delete.gif" alt="删除" title="删除" /></a> 
            <a href="javascript:void(0)" onclick="reupload_pic(this,<?php echo $v['id'];?>)"><img src="img/re_upload.gif" alt="重新上传" title="重新上传此图片" /></a> 
            <a href="javascript:void(0)" onclick="set_pic_cover(this,<?php echo $v['id'];?>)"><img src="img/cover.gif" alt="设为封面" title="设为封面" /></a> 
            <a href="javascript:void(0)" onclick="move_pic_to(2,this,<?php echo $v['id'];?>)"><img src="img/moveto.gif" alt="移动到相册" title="移动到相册" /></a></span>
        <div class="cb"><input type="checkbox" name="picture[]" value="<?php echo $v['id'];?>" onclick="select_pic(this,<?php echo $v['id'];?>)" /></div>
        <div class="selected"></div>
    </li>
    <?php
    endforeach;
    else:
        echo "<li>无图片</li>";
    endif;
    ?>
    </ul>
    </form>
    <div class="pageset"><?php echo $res->get('pageset');?></div>
</div>
<script type="text/javascript">
init_submit_bat();
</script>
<?php include('foot.php');?>