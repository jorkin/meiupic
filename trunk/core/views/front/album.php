<?php include('head.php');?>
<div class="main box1">
    <div class="bg1 title"><h3>相册列表</h3></div>
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
            <?php $ls = $res->get('albums');
            if($ls):
            foreach($ls as $k=>$v):
                if($k != 0 && $k%5 == 0){
                    echo '</tr><tr>';
                }
            ?>
            <td class="albumtd" id="i_<?php echo $v['id'];?>">
                <a href="index.php?ctl=album&act=photos&album=<?php echo $v['id']; ?>"><img src="<?php echo $v['cover'];?>" /></a>
                <div class="line35">
                   <a href="index.php?ctl=album&act=photos&album=<?php echo $v['id']; ?>"><?php echo $v['name'];?></a>
                </div>
            </td>
            <?php 
            endforeach;
            else:
            ?>
            <td><div class="warning"> 当前没有任何相册！ </div></td>
            <?php
            endif;
            ?>
           </tr>
        </table>
        
        <div class="pageset"><?php echo $res->get('pageset');?></div>
    </div>
</div>
<?php include('foot.php');?>