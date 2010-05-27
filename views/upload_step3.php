<?php include('head.php');?>
<div id="upload_help"> 
    <span>第一步：选择相册并上传图片</span>  >> 
    <span>第二步：上传图片</span> >>
    <span class="current">第三步：查看结果修改图片名称</span> >>
    <span>完成</span>
</div>

<div id="save_album">
    <form method="post" action="index.php?ctl=upload&act=saveimgname&album=<?php echo $res->get('album'); ?>">
    <ul class="album">
        <?php 
        $ls = $res->get('uploaded_pics');
        if($ls):
        foreach($ls as $v):
        ?>
        <li rel="<?php echo SITE_URL.imgSrc($v['path']); ?>"><span class="img"><img src="<?php echo imgSrc($v['thumb']); ?>" /></span><span class="info"><input class="ipt_2" name="imgname[<?php echo $v['id'];?>]" value="<?php echo $v['name'];?>"></span><span class="control"><a href="javascript:void(0)" onclick="copyUrl(this)"><img src="img/copyu.gif" alt="复制网址" title="复制网址" /></a> <a href="javascript:void(0)" onclick="copyCode(this)"><img src="img/copyc.gif" alt="复制代码" title="复制代码" /></a></span></li>
        <?php
        endforeach;
        endif;
        ?>
    </ul>
    
    <div class="buttons"><input class="btn" type="submit" value="保存" /></div>
    </form>
</div>

<?php include('foot.php');?>