<?php include('head.php');?>
<div id="upload_help"> 
    <span class="current">第一步：选择相册并上传图片</span>  >> 
    <span>第二步：上传图片</span> >>
    <span>第三步：查看结果修改图片名称</span> >>
    <span>完成</span>
</div>

<div id="sel_album">
    选择相册：
    <select name="albums">
        <?php 
            $ls = $res->get('albums_list');
            if($ls){
                foreach($ls as $v){
                    echo "<option value=\"".$v['id']."\">".$v['name']."</option>\n";
                }
            }
        ?>
    </select> <a href="javascript:void(0);" onclick="create_album(0)">新建相册</a>
    
    <div class="buttons"><input class="btn" type="button" value="下一步" onclick="go_upload_step2()" /></div>
</div>

<?php include('foot.php');?>