<?php include('head.php');?>
<div id="upload_help"> 
    <span>第一步：选择相册并上传图片</span>  >> 
    <span>第二步：上传图片</span> >>
    <span class="current">第三步：查看结果</span> >>
    <span>完成</span>
</div>

<div id="save_album">
    <div class="line"><img src="img/loading.gif" /></div>
    <div class="line" style="font-size:36px;line-height:80px;">上传成功，程序正在处理照片!</div>
    <div class="line">您也可以<a href="admin.php?ctl=album&act=photos&album=<?php echo $res->get('album');?>">离开此页面</a>让后端自动处理！</div>
</div>

<?php include('foot.php');?>
<script type="text/javascript" src="admin.php?ctl=photo&act=resize&album=<?php echo $res->get('album');?>&time=<?php echo time();?>"></script>