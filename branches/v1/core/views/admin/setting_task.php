<?php include('head.php');?>
<?php $setting = $res->get('setting');?>

<div id="setting_box">
    <div id="setting_nav">
        <?php include('setting_nav.php'); ?>
    </div>
    <div id="setting_body">
        <div id="operator_setting" class="stab">
            <div style="text-align:left;"><input type="button" class="btn" value="处理所有图片" onclick="window.location.href='admin.php?ctl=setting&act=dotask';"></div>
            <table>
                <?php 
                if($res->get('tasks')):
                foreach($res->get('tasks') as $v): ?>
                <tr>
                    <td><?php echo $v['name'] ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$v['create_time']);?></td>
                    <td>待生成缩略图</td>
                </tr>
                <?php endforeach;
                endif; ?>
            </table>
            <div class="pageset"><?php echo $res->get('pageset');?></div>
        </div>
    </div>
</div>
<?php include('foot.php');?>