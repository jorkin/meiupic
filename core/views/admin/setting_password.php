<?php include('head.php');?>
<?php $setting = $res->get('setting');?>

<div id="setting_box">
    <div id="setting_nav">
        <?php include('setting_nav.php'); ?>
    </div>
    <div id="setting_body">
        <div id="change_pass" class="stab">
        <form method="post" action="admin.php?ctl=setting&act=password" onsubmit="check_form(this);return false;">
        <table>
            <tr>
                <td class="tt">原密码:</td>
                <td><input class="txtinput" type="password" name="oldpass" /></td>
            </tr>
            <tr>
                <td class="tt">新密码:</td>
                <td><input class="txtinput" type="password" name="newpass" /></td>
            </tr>
            <tr>
                <td class="tt">再次输入密码:</td>
                <td><input class="txtinput" type="password" name="passagain" /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" class="btn" value="修改密码" /></td>
            </tr>
        </table>
        </form>
        </div>
    </div>
</div>
<?php include('foot.php');?>