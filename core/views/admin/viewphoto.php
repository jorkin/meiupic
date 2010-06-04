<?php include('head.php');?>

<?php $ls = $res->get('pic');?>
<div id="allpic">
    <div id="album_nav" class="album_detail">
        <h1 class="album_title"><?php echo $ls['name'];?></h1>
        <div class="photoinfo"><a href="index.php?ctl=album&act=photos&album=<?php echo $ls['album'];?>">返回相册《<?php echo $res->get('album_name'); ?>》</div>
    </div>
    <div id="photo-body">
         <div class="picnt">
              <div class="sh1">
              <div class="sh2">
              <div class="sh3">
              <a class="p-tag" hidefocus="true" title="点击查看下一张" href="#photo-body"><img class="p-tag" width="700" src="<?php echo imgSrc($ls['path']);?>"></a>
              </div>       
              </div>      
              </div>     
         </div>    
    </div>
    <div class="clearfix"></div>
</div>
<?php include('foot.php');?>