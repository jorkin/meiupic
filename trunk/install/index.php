<?php

define('FCPATH',__FILE__);
define('ROOTDIR',dirname(dirname(FCPATH)).DIRECTORY_SEPARATOR);
define('INSDIR',dirname(FCPATH).DIRECTORY_SEPARATOR);


require_once(INSDIR.'include/install_func.php');

define('COREDIR',ROOTDIR.'core'.DIRECTORY_SEPARATOR);
define('LIBDIR',COREDIR.'libs'.DIRECTORY_SEPARATOR);
define('INCDIR',COREDIR.'include'.DIRECTORY_SEPARATOR);
define('CTLDIR',COREDIR.'ctls'.DIRECTORY_SEPARATOR);
define('VIEWDIR',COREDIR.'views'.DIRECTORY_SEPARATOR);
define('MODELDIR',COREDIR.'models'.DIRECTORY_SEPARATOR);
define('DATADIR',ROOTDIR.'data'.DIRECTORY_SEPARATOR);
define('PLUGINDIR',ROOTDIR.'plugins'.DIRECTORY_SEPARATOR);
if (floor(PHP_VERSION) < 5){
    define('PHPVer',4);
}else{
    define('PHPVer',5);
}
require_once(COREDIR.'loader.php');
require_once(INSDIR.'include/install_var.php');
require_once(INSDIR.'include/lang.php');

$allow_method = array('license', 'env','db_init', 'feedback', 'complete');

$step = intval(r('step')) ? intval(r('step')) : 0;
$method = r('method');

if(empty($method) || !in_array($method, $allow_method)) {
    $method = isset($allow_method[$step]) ? $allow_method[$step] : '';
}

timezone_set();

if(empty($method)) {
    show_msg('method_undefined', $method, 0);
}

if(file_exists($lockfile) && $method != 'complete') {
    show_msg('install_locked', '', 0);
}

if($method == 'license'){
    show_license();
}elseif($method == 'env'){
    env_check($env_items);
    dirfile_check($dirfile_items);
    show_env_result($env_items, $dirfile_items, $func_items);
}elseif($method == 'db_init'){
    $default_config = $CONFIG = array();
    $default_configfile = './conf/config.default.php';
    
    if(!file_exists(ROOTDIR.$default_configfile)) {
        exit('config_default.php was lost, please reupload this file.');
    } else {
        include ROOTDIR.$default_configfile;
        $default_config = $CONFIG;
    }

    if(file_exists($confile)) {
        include $confile;
    } else {
        $CONFIG = $default_config;
    }
    
    $dbhost = $CONFIG['database']['default']['host'];
    $dbname = $CONFIG['database']['default']['dbname'];
    $dbport = $CONFIG['database']['default']['port'];
    $dbpw = $CONFIG['database']['default']['dbpass'];
    $dbuser = $CONFIG['database']['default']['dbuser'];
    $tablepre = $CONFIG['database']['default']['pre'];

    $adminemail = 'admin@admin.com';
    $PHP_SELF = htmlspecialchars($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
    $url = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].preg_replace("/\/+(api|archiver|wap)?\/*$/i", '', substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'))));
    $siteurl = substr($url, 0, -7);
    
    $sitename = lang('myalbum');
    $submit = true;
    $error_msg = array();
    $dbadapter = p('dbadapter');
    
    if(isset($form_db_init_items) && is_array($form_db_init_items)) {
        foreach($form_db_init_items as $key => $items) {
            $$key = p($key);
            if($dbadapter == 'sqlite' && $key == 'mysqldbinfo'){
                continue;
            }
            if(!isset($$key) || !is_array($$key)) {
                $submit = false;
                break;
            }
            foreach($items as $k => $v) {
                $tmp = $$key;
                $$k = isset($tmp[$k])?$tmp[$k]:'';
                if(empty($$k) || !preg_match($v['reg'], $$k)) {
                    if(empty($$k) && !$v['required']) {
                        continue;
                    }
                    $submit = false;
                    $error_msg[$key][$k] = 1;
                }
            }
        }
    } else {
        $submit = false;
    }
    
    if($submit && $_SERVER['REQUEST_METHOD'] == 'POST') {
        if($password != $password2) {
            $error_msg['admininfo']['password2'] = 1;
            $submit = false;
        }
        $forceinstall = isset($_POST['mysqldbinfo']['forceinstall']) ? $_POST['mysqldbinfo']['forceinstall'] : '';
        $dbname_not_exists = true;
        if(!empty($dbhost) && $dbadapter=='mysql' && empty($forceinstall)) {
            $dbname_not_exists = check_db($dbhost, $dbuser, $dbpw, $dbname, $tablepre);
            if(!$dbname_not_exists) {
                $form_db_init_items['mysqldbinfo']['forceinstall'] = array('type' => 'checkbox', 'required' => 0, 'reg' => '/^.*+/');
                $error_msg['mysqldbinfo']['forceinstall'] = 1;
                $submit = false;
                $dbname_not_exists = false;
            }
        }
        $forceinstall = isset($_POST['sqlite']['forceinstall']) ? $_POST['sqlite']['forceinstall'] : '';
        if($dbadapter=='sqlite' && file_exists(ROOTDIR.$dst_dbfile) && empty($forceinstall)){
            $form_db_init_items['sqlite']['forceinstall'] = array('type' => 'checkbox', 'required' => 0, 'reg' => '/^.*+/');
            $error_msg['sqlite']['forceinstall'] = 1;
            $submit = false;
        }
    }
    
    if($submit) {
        if($dbadapter == 'mysql'){
            if($username && $email && $password) {
                if(strlen($username) > 15 || preg_match("/^$|^c:\\con\\con$|　|[,\"\s\t\<\>&]|^Guest/is", $username)) {
                    show_msg('admin_username_invalid', $username, 0);
                } elseif(!strstr($email, '@') || $email != stripslashes($email) || $email != htmlspecialchars($email)) {
                    show_msg('admin_email_invalid', $email, 0);
                }
            } else {
                show_msg('admininfo_invalid', '', 0);
            }
            
            $step = $step + 1;
            if(empty($dbname)) {
                show_msg('dbname_invalid', $dbname, 0);
            } else {
                if(!$link = @mysql_connect($dbhost.':'.$dbport, $dbuser, $dbpw)) {
                    $errno = mysql_errno($link);
                    $error = mysql_error($link);
                    if($errno == 1045) {
                        show_msg('database_errno_1045', $error, 0);
                    } elseif($errno == 2003) {
                        show_msg('database_errno_2003', $error, 0);
                    } else {
                        show_msg('database_connect_error', $error, 0);
                    }
                }

                if(mysql_get_server_info() > '4.1') {
                    mysql_query("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8", $link);
                } else {
                    mysql_query("CREATE DATABASE IF NOT EXISTS `$dbname`", $link);
                }

                if(mysql_errno()) {
                    show_msg('database_errno_1044', mysql_error(), 0);
                }
                mysql_close($link);
            }

            if(strpos($tablepre, '.') !== false) {
                show_msg('tablepre_invalid', $tablepre, 0);
            }

            if(function_exists('mysqli_connect')){
                $CONFIG['database']['default']['adapter'] = 'mysqli';
            }
            $CONFIG['database']['default']['host'] = $dbhost;
            $CONFIG['database']['default']['port'] = $dbport;
            $CONFIG['database']['default']['dbname'] = $dbname;
            $CONFIG['database']['default']['dbuser'] = $dbuser;
            $CONFIG['database']['default']['dbpass'] = $dbpw;
            $CONFIG['database']['default']['pre'] = $tablepre;
            $CONFIG['cookie_name'] = 'MPIC_'.random(4);
            $CONFIG['cookie_auth_key'] = random(12);
            $CONFIG['img_engine'] = class_exists('imagick')?'imagick':'gd';
            
            save_config_file($confile, $CONFIG, $default_config);
            $db =& loader::database();
        
            show_header();
            show_install();
            $sql = file_get_contents($sqlfile);
            $sql = str_replace("\r\n", "\n", $sql);
            runquery($sql);
            $datasql = file_get_contents($datasqlfile);
            runquery($datasql);
            showjsmessage(lang('install_data_sql').lang('succeed'));
        }elseif($dbadapter == 'sqlite'){
            $step = $step + 1;
                        
            show_header();
            show_install();
            
            $CONFIG['database']['default']['adapter'] = 'sqlite';
            $CONFIG['database']['default']['dbpath'] = $dst_dbfile;
            $CONFIG['database']['default']['pre'] = 'meu_';
            $CONFIG['cookie_name'] = 'MPIC_'.random(4);
            $CONFIG['cookie_auth_key'] = random(12);
            $CONFIG['img_engine'] = class_exists('imagick')?'imagick':'gd';
            
            save_config_file($confile, $CONFIG, $default_config);
            if(copy($sqlitefile,ROOTDIR.$dst_dbfile)){
                showjsmessage(lang('copy_sqlite').$dst_dbfile.lang('succeed'));
            }else{
                showjsmessage(lang('copy_sqlite').$dst_dbfile.lang('failed'));
            }
            
            $db =& loader::database();
        }
        
        dir_clear(ROOTDIR.'cache/data');
        dir_clear(ROOTDIR.'cache/templates');
        dir_clear(ROOTDIR.'cache/tmp');
    
        $db->insert('#@users',array('user_name'=>$username,'user_nicename'=>$username,'user_pass'=>md5($password),'create_time'=>time()));
        if($db->query()){
            showjsmessage(lang('create_admin_account').lang('succeed'));
        }else{
            showjsmessage(lang('create_admin_account').lang('failed'));
        }
        $theme_info = ROOTDIR.'themes/default/info.php';
        if(file_exists($theme_info)){
            include($theme_info);
            $insert_arr = array('name'=>$theme_name,'cname'=>'default','copyright'=>$theme_copyright,'config'=>serialize($theme_config));
            $db->insert('#@themes',$insert_arr);
            $db->query();
            showjsmessage(lang('install_default_theme'));
        }else{
            show_msg('miss_default_theme');
        }
        
        $mdl_setting = loader::model('setting');
        $mdl_setting->set_conf('system.version',MPIC_VERSION);
        $mdl_setting->set_conf('system.installed_time',time());
        $mdl_setting->set_conf('site.title',$sitename);
        $mdl_setting->set_conf('site.url',$siteurl);
        $mdl_setting->set_conf('site.email',$email);
        
        showjsmessage(lang('update_user_setting'));
    
        echo '<script type="text/javascript">document.getElementById("laststep").disabled=false;document.getElementById("laststep").value = \''.lang('installed_complete').'\';</script><script type="text/javascript">setTimeout(function(){window.location=\'index.php?method=complete\'}, 3000);</script>'."\r\n";
        @touch(ROOTDIR.'conf/install.lock');
        show_footer();
    }else{
        show_form($form_db_init_items, $error_msg);
    }
}elseif($method == 'complete'){
    $step = 4;
    show_header();
    
    echo '<ul style="line-height: 200%; margin-left: 30px;">';
	echo '<li><a href="../">'.lang('install_succeed').'</a><br>';
	echo '<script>setTimeout(function(){window.location=\'../\'}, 2000);</script>'.lang('auto_redirect').'</li>';
	echo '</ul></div>';
    show_footer();
}