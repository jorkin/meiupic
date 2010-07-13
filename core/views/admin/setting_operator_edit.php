<?php include('head.php');?>
<?php $info = $res->get('operator'); ?>
<div id="setting_box">
    <div id="setting_nav">
        <?php include('setting_nav.php'); ?>
    </div>
    <div id="setting_body">
        <div id="operator_setting" class="stab">
            <h1 class="album_title1">修改管理员密码</h1>
            <form method="post" action="admin.php?ctl=setting&act=operator_edit">
                <input type="hidden" name="id" value="<?php echo $info['id'];?>" />
                <table>
                    <tr><td class="tt">登录名</td><td><?php echo $info['username'];?></td></tr>
                    <tr><td class="tt">密码</td><td><input type="password" name="userpass" class="txtinput" /></td></tr>
                    <tr><td class="tt">重复密码</td><td><input type="password" name="passagain" class="txtinput" /></td></tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" class="btn" value="确定修改" /></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php include('foot.php');?>