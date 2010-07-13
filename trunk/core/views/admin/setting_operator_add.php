<?php include('head.php');?>

<div id="setting_box">
    <div id="setting_nav">
        <?php include('setting_nav.php'); ?>
    </div>
    <div id="setting_body">
        <div id="operator_setting" class="stab">
            <h1 class="album_title1">添加管理员</h1>
            <form method="post" action="admin.php?ctl=setting&act=operator_add">
                <table>
                    <tr><td class="tt">登录名</td><td><input type="text" name="username" class="txtinput" /></td></tr>
                    <tr><td class="tt">密码</td><td><input type="password" name="userpass" class="txtinput" /></td></tr>
                    <tr><td class="tt">重复密码</td><td><input type="password" name="passagain" class="txtinput" /></td></tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" class="btn" value="确定添加" /></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php include('foot.php');?>