<?php include('head.php');?>
<link rel="stylesheet" href="img/plupload/plupload.queue.css" type="text/css" />
<script type="text/javascript" src="js/gears_init.js"></script>
<script type="text/javascript" src="js/plupload.full.min.js"></script>
<script type="text/javascript" src="js/jquery.plupload.queue.min.js"></script>
<script>


$(function() {
	plupload.addI18n({
    	'Select files' : '选择图片',
    	'Add files to the upload queue and click the start button.' : '添加图片并点击开始按钮,可以同时选择多个图片。',
    	'Filename' : '文件名',
    	'Status' : '状态',
    	'Size' : '大小',
    	'Add files' : '添加文件',
    	'Stop current upload' : '停止上传',
    	'Start uploading queue' : '开始上传',
    	'Start upload' : '开始上传',
    	'Drag files here.' : '拖拽文件至此处.'
    });
    
	$("#flash_uploader").pluploadQueue({
		runtimes : 'html5,flash,gears,silverlight,browserplus,html4',
		url : 'admin.php?ctl=upload&act=process',
		max_file_size : '10mb',
		chunk_size : '1mb',
		unique_names : true,
		filters : [
			{title : "Image files", extensions : "jpg,jpeg,gif,png"}
		],
		<?php if($res->get('open_pre_resize')):?>resize : {width : <?php echo $res->get('resize_img_width');?>, height : <?php echo $res->get('resize_img_height');?>, quality : <?php echo $res->get('resize_quality');?>},
<?php endif;?>
		flash_swf_url : 'js/plupload.flash.swf',
		silverlight_xap_url : 'js/plupload.silverlight.xap'
	});
	
    var usubmited = 0;
	$('#upload_photos_form').submit(function(e) {
    	var uploader = $('#flash_uploader').pluploadQueue();
		if (uploader.total.uploaded == 0) {
			if (uploader.files.length > 0) {
				uploader.bind('UploadProgress', function() {
					if (uploader.total.uploaded == uploader.files.length && usubmited == 0){
						$('#upload_photos_form').submit();
						usubmited = 1;
					}
				});

				uploader.start();
			} else
				alert('至少选择一个文件上传.');

			e.preventDefault();
		}
	});
});
</script>

<div id="upload_help"> 
    <span>第一步：选择相册并上传图片</span>  >> 
    <span class="current">第二步：上传图片</span> >>
    <span>第三步：查看结果</span> >>
    <span>完成</span>
</div>

<div id="upload_field">
    <form id="upload_photos_form" method="post" action="admin.php?ctl=upload&act=doupload&album=<?php echo $res->get('album_id')?>">
    <div id="flash_uploader" style="width: 100%; height: 330px;margin-top:10px;">
        Loading....
    </div>
    <div align="center"><input type="submit" class="btn" value="下一步" /></div>
    </form>
    
    <a href="admin.php?ctl=upload&act=step2_1&album_id=<?php echo $res->get('album_id')?>">普通上传模式</a>
</div>

<?php include('foot.php');?>