<?php include('head.php');?>
<div class="main box1">
    <div class="bg1 title"><h3 id="album_ptitle">相册名 &gt; 照片名</h3></div>
    <div class="box_body">
        <ul>
            <?php $ls = $res->get('piclist');
            if($ls):
            foreach($ls as $k=>$v):
            ?>
            <li><img src="<?php echo mkImgLink($v['dir'],$v['pickey'],$v['ext'],'square');?>" /></li>
            <?php
            endforeach;
            endif;
            ?>
        </ul>
    </div>
</div>
<?php include('foot.php');?>