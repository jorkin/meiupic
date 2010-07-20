<?php include('head.php');?>
<div class="main box1">
    <div class="bg1 title"><h3 id="album_ptitle"><a href="index.php?ctl=album">相册列表</a> &gt; <?php echo $res->get('album_name');?></h3></div>
    <div class="box_body">
    <table class="table100">
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
      <tr>
        <?php $ls = $res->get('piclist');
        if($ls):
        foreach($ls as $k=>$v):
            if($k != 0 && $k%5 == 0){
                echo '</tr><tr>';
            }
        ?>
        <td class="phototd" id="i_<?php echo $v['id'];?>">
            <a href="index.php?ctl=photo&act=view&album=<?php echo $v['album']; ?>#photo=<?php echo $v['id'];?>"><img src="<?php echo mkImgLink($v['dir'],$v['pickey'],$v['ext'],'thumb');?>" /></a>
            <div class="line35">
                <a href="index.php?ctl=photo&act=view&album=<?php echo $v['album']; ?>#photo=<?php echo $v['id'];?>"><?php echo $v['name'];?></a>
            </div>
            <div>浏览数：<?php echo $v['hits'];?></div>
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
    
     <div class="pageset"><?php echo $res->get('pageset');?></div>
    </div>
</div>
<?php include('foot.php');?>