<?php include('head.php');?>
<div class="main box1">
    <div class="bg1 title"><h3>热门照片</h3></div>
    <div class="box_body">
    <table class="table100">
      <tr>
        <?php $ls = $res->get('piclist');
        if($ls):
        foreach($ls as $k=>$v):
            if($k != 0 && $k%3 == 0){
                echo '</tr><tr>';
            }
        ?>
        <td class="phototd" id="i_<?php echo $v['id'];?>">
            <img src="<?php echo SITE_URL.mkImgLink($v['dir'],$v['pickey'],$v['ext'],'small');?>" />
            <div class="line35">
                <?php echo $v['name'];?>
            </div>
        </td>
        <?php 
        endforeach;
        endif;
        ?>
       </tr>
    </div>
</div>
<?php include('foot.php');?>