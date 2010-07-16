<?php include('head.php');?>

<div id="upload_help"> 
    <span>第一步：选择相册并上传图片</span>  >> 
    <span class="current">第二步：上传图片</span> >>
    <span>第三步：查看结果</span> >>
    <span>完成</span>
</div>

<div id="upload_field">
    <form id="upload_photos_form" method="post" enctype="multipart/form-data" action="admin.php?ctl=upload&act=dopicupload&album=<?php echo $res->get('album_id')?>">
    <div id="uploader" style="width: 100%; height: 210px;margin:10px;">
        请选择您要上传的图片：<br /><br />
        1. <input type="file" name="imgs[]" /><br /><br />
        2. <input type="file" name="imgs[]" /><br /><br />
        3. <input type="file" name="imgs[]" /><br /><br />
        4. <input type="file" name="imgs[]" /><br /><br />
        5. <input type="file" name="imgs[]" /><br /><br />
    </div>
    <div align="left"><input type="submit" class="btn" value="下一步" /></div>
    </form>
    <br />
    <a href="admin.php?ctl=upload&act=step2&album_id=<?php echo $res->get('album_id')?>">高级上传模式</a>
</div>

<?php include('foot.php');?>