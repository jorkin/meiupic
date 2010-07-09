<?php include('head.php');?>
<div class="main box1">
    <div class="bg1 title"><h3>热门照片</h3></div>
    <div class="box_body">
    <table class="table100">
      <tr>
        <?php $ls = $res->get('piclist');
        if($ls):
        foreach($ls as $k=>$v):
            if($k != 0 && $k%5 == 0){
                echo '</tr><tr>';
            }
        ?>
        <td class="phototd" id="i_<?php echo $v['id'];?>">
            <a href="index.php?ctl=photo&act=view&album=<?php echo $v['album']; ?>#photo=<?php echo $v['id'];?>"><img src="<?php echo SITE_URL.mkImgLink($v['dir'],$v['pickey'],$v['ext'],'thumb');?>" /></a>
            <div class="line35">
                <a href="index.php?ctl=photo&act=view&album=<?php echo $v['album']; ?>#photo=<?php echo $v['id'];?>"><?php echo $v['name'];?></a>
            </div>
        </td>
        <?php 
        endforeach;
        else:
        ?>
        <td><div class="warning"> 当前没有任何图片！ </div></td>
        <?php
        endif;
        ?>
       </tr>
    </table>
    </div>
</div>
<?php include('foot.php');?>