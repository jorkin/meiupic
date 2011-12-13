<?php
if(!defined('IN_MEIU')) exit('Access Denied');

/*重新组织创建表的SQL*/
function _createtable($sql) {
    $db =& loader::database();
    
    $type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
    $type = in_array($type, array('MYISAM', 'HEAP', 'MEMORY')) ? $type : 'MYISAM';
    return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
    ($db->version() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=utf8" : " TYPE=$type");
}

@set_time_limit(0);
@ignore_user_abort(true);

$error = '';
if(getPost('_upgrade_act') == 'login'){
    $login_name = getPost('login_name');
    $login_pass = getPost('login_pass');
    if(!$login_name){
        $error .= '<div>'.lang('username_empty').'</div>';
    }
    if(!$login_pass){
        $error .=  '<div>'.lang('userpass_empty').'</div>';
    }
    if($login_name && $login_pass && !$user->set_login($login_name,md5($login_pass)) ){
        $error .= '<div>'.lang('username_pass_error').'</div>';
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo lang('upgrade_title',MPIC_VERSION);?></title>
<style>
    body{
        font-size:12px;
    }
</style>
</head>

<body>
<h1><?php echo lang('upgrade_title',MPIC_VERSION);?></h1>
<?php
//如果没有登录输出登录框
if(!$user->loggedin()){
    echo '<h2>'.lang('upgrade_need_login').'</h2>';
    echo '<div style="color:red">'.$error.'</div><form id="login_form" action="" method="post">
        <input type="hidden" name="_upgrade_act" value="login" />
        <div class="field">
            <div class="label">'.lang('username').'</div>
            <div class="ipts"><input type="text" name="login_name" class="inputstyle iptw2" value="" /></div>
            <div class="clear"></div>
        </div>
        <div class="field">
            <div class="label">'.lang('password').'</div>
            <div class="ipts"><input type="password" name="login_pass" class="inputstyle iptw2" value="" /></div>
            <div class="clear"></div>
        </div>
        <div class="field">
            <div class="label"> &nbsp;</div>
            <div class="ipts"><input type="submit" value="'.lang('login').'" class="ylbtn f_left" name="submit"></div>
            <div class="clear"></div>
        </div>
    </form>';
}else{
    $prev_version = $setting_mdl->get_conf('system.version');
    $current_version = MPIC_VERSION;
    if($current_version == $prev_version){
        echo lang('have_been_updated').'<br />';
        exit;
    }
    //如果没有获取到当前version，根据数据库判断
    $db =& loader::database();
    if($prev_version == ''){
        $rows = $db->show_tables();
        if(!in_array($db->pre.'setting',$rows)){
            $current_version = '1.1';
        }elseif(!in_array($db->pre.'cate',$rows)){
            $current_version = '2.0';
        }else{
            $prev_version = $current_version;
        }
    }

    if(version_compare($current_version,$prev_version,'<')){
        echo lang('could_not_degrade').'<br />';
        exit;
    }
    if($prev_version == '' || version_compare($prev_version,'2.0','<') ){
        echo lang('too_old_to_update').'<br />';
        exit;
    }
    
    $script_file = ROOTDIR.'install/upgrade_'.$prev_version.'.php';
    if(file_exists($script_file)){
        require_once($script_file);
    }
    
    $setting_mdl->set_conf('system.version',MPIC_VERSION);
    $setting_mdl->set_conf('update.return','lastest');
    //清除缓存
    //Todo 需要统一清除缓存的功能，使其兼容memcache等
    dir_clear(ROOTDIR.'cache/data');
    dir_clear(ROOTDIR.'cache/templates');
    dir_clear(ROOTDIR.'cache/tmp');

    echo lang('upgrade_success').' <a href="'.site_link('default','index').'">'.lang('click_to_jump').'</a>';
}
?>
</body>
</html>