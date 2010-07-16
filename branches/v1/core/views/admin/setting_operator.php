<?php include('head.php');?>
<?php $setting = $res->get('setting');?>

<div id="setting_box">
    <div id="setting_nav">
        <?php include('setting_nav.php'); ?>
    </div>
    <div id="setting_body">
        <div id="operator_setting" class="stab">
            <table>
                <?php foreach($res->get('mlist') as $v): ?>
                <tr>
                    <td><?php echo $v['username'] ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$v['create_time']);?></td>
                    <td><a href="admin.php?ctl=setting&act=operator_edit&id=<?php echo $v['id']; ?>">修改密码</a> <a href="javascript:void(0)" onclick="if(confirm('确认删除？')){ window.location.href='admin.php?ctl=setting&act=operator_del&id=<?php echo $v['id']; ?>' }">删除</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <div style="text-align:left;"><input type="button" class="btn" value="添加管理员" onclick="window.location.href='admin.php?ctl=setting&act=operator_add';"></div>
            <div class="pageset"><?php echo $res->get('pageset');?></div>
        </div>
    </div>
</div>
<?php include('foot.php');?>