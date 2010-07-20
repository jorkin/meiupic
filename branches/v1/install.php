<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */
 error_reporting(E_ERROR);
 header("Content-type: text/html; charset=utf-8");
 
    function get_basepath(){
        if ($dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/')) {
          $base_path = "/$dir";
          $base_path .= '/';
        } else {
          $base_path = '/';
        }
        return $base_path;
    }

    function get_gd_version()
    {
        static $version = -1;

        if ($version >= 0)
        {
            return $version;
        }

        if (!extension_loaded('gd'))
        {
            $version = 0;
        }
        else
        {
            // 尝试使用gd_info函数
            if (PHP_VERSION >= '4.3')
            {
                if (function_exists('gd_info'))
                {
                    $ver_info = gd_info();
                    preg_match('/\d/', $ver_info['GD Version'], $match);
                    $version = $match[0];
                }
                else
                {
                    if (function_exists('imagecreatetruecolor'))
                    {
                        $version = 2;
                    }
                    elseif (function_exists('imagecreate'))
                    {
                        $version = 1;
                    }
                }
            }
            else
            {
                if (preg_match('/phpinfo/', ini_get('disable_functions')))
                {
                    /* 如果phpinfo被禁用，无法确定gd版本 */
                    $version = 1;
                }
                else
                {
                  // 使用phpinfo函数
                   ob_start();
                   phpinfo(8);
                   $info = ob_get_contents();
                   ob_end_clean();
                   $info = stristr($info, 'gd version');
                   preg_match('/\d/', $info, $match);
                   $version = $match[0];
                }
             }
        }

        return $version;
     }
    
    function get_system_info()
    {
        $system_info = array();

        /* 检查系统基本参数 */
        $system_info[] = array("操作系统", PHP_OS);
        $system_info[] = array("php版本", PHP_VERSION);

        /* 检查MYSQL支持情况 */
        $mysql_enabled = function_exists('mysql_connect') ? "支持" : "<b class=\"red\">不支持</b>";
        $system_info[] = array("Mysql 数据库支持", $mysql_enabled);
        
        /* 检查Sqlite支持情况*/
        $sqlite_enabled = function_exists('sqlite_open') ? "支持" : "<b class=\"red\">不支持</b>";
        $system_info[] = array("Sqlite 数据库支持", $sqlite_enabled);
        
        /* 检查Rewrite支持情况*/
        if ( function_exists('apache_get_modules') && in_array('mod_rewrite',apache_get_modules()) ){
            $rewrite_enabled =  '支持';
        }else{
            $rewrite_enabled =  '<b class=\"red\">不支持</b>';
        }
        $system_info[] = array("Rewrite 支持", $rewrite_enabled);
        /* 检查图片处理函数库 */
        $gd_ver = get_gd_version();
        $gd_ver = empty($gd_ver) ? "<b class=\"red\">不支持</b>" : $gd_ver;
        if ($gd_ver > 0)
        {
            if (PHP_VERSION >= '4.3' && function_exists('gd_info'))
            {
                $gd_info = gd_info();
                $jpeg_enabled = ($gd_info['JPG Support']        === true) ? "支持" : "不支持";
                $gif_enabled  = ($gd_info['GIF Create Support'] === true) ? "支持" : "不支持";
                $png_enabled  = ($gd_info['PNG Support']        === true) ? "支持" : "不支持";
            }
            else
            {
                if (function_exists('imagetypes'))
                {
                    $jpeg_enabled = ((imagetypes() & IMG_JPG) > 0) ? "支持" : "不支持";
                    $gif_enabled  = ((imagetypes() & IMG_GIF) > 0) ? "支持" : "不支持";
                    $png_enabled  = ((imagetypes() & IMG_PNG) > 0) ? "支持" : "不支持";
                }
                else
                {
                    $jpeg_enabled = "不支持";
                    $gif_enabled  = "不支持";
                    $png_enabled  = "不支持";
                }
            }
        }
        else
        {
            $jpeg_enabled = "不支持";
            $gif_enabled  = "不支持";
            $png_enabled  = "不支持";
        }
        $system_info[] = array("GD库", $gd_ver);
        $system_info[] = array("JPG组件支持", $jpeg_enabled);
        $system_info[] = array("GIF组件支持",  $gif_enabled);
        $system_info[] = array("PNG组件支持",  $png_enabled);
        
        /* 检查EXIF支持情况*/
        $exif_enabled = function_exists('exif_read_data') ? "支持" : "<b class=\"red\">不支持</b>";
        $system_info[] = array("EXIF 支持", $exif_enabled);
        
        /* 服务器是否安全模式开启 */
        $safe_mode = ini_get('safe_mode') == '1' ? "打开" : "关闭";
        $system_info[] = array("安全模式", $safe_mode);
        
        return $system_info;
    }
    
    function creat_mysql($setting){
        $dbconn = @mysql_connect($setting['dbhost'].':'.$setting['dbport'],$setting['dbuser'],$setting['dbpass']);
        if(!$dbconn){
            echo "无法连接数据库！请确认数据库参数是否正确！Error:".mysql_error();
            exit();
        }
        $select_db = @mysql_select_db($setting['dbname'],$dbconn);
        if(!$select_db){
            if(!isset($_POST['create_db'])){
                echo "无法选择库！请确认数据库参数是否正确！Error:".mysql_error();
                exit();
            }
            $create_db = mysql_query('CREATE DATABASE `'.$setting['dbname'].'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;',$dbconn);
            
            if(!$create_db){
                echo "创建数据库失败，可能是权限错误！Error:".mysql_error();
                exit();
            }
            
            $select_db = @mysql_select_db($setting['dbname'],$dbconn);
        }
        
        $config_file_content = "<?php\n";
        $config_file_content .= "\$db_config = array(\n";
        $config_file_content .= "'adapter'  => '".$setting['dbadapter']."',\n";
        $config_file_content .= "'host'     => '".$setting['dbhost']."',\n";
        $config_file_content .= "'port'     => '".$setting['dbport']."',\n";
        $config_file_content .= "'dbuser'   => '".$setting['dbuser']."',\n";
        $config_file_content .= "'dbpass'   => '".$setting['dbpass']."',\n";
        $config_file_content .= "'dbname'   => '".$setting['dbname']."',\n";
        $config_file_content .= "'pconnect' => false,\n";
        $config_file_content .= "'charset'  => 'utf8',\n";
        $config_file_content .= "'pre'      => '".$setting['dbpre']."'\n";
        $config_file_content .= ");\n";
        $config_file_content .= "?>";
        if(!@file_put_contents(ROOTDIR.'conf/config.php',$config_file_content)){
            echo "无法创建数据库配置文件！";
            exit();
        }
        @chmod(ROOTDIR.'conf/config.php',0755);
        mysql_query('SET NAMES "utf8"',$dbconn);
        
        $admintable = $setting['dbpre'].'admin';
        $albumstable = $setting['dbpre'].'albums';
        $imgstable = $setting['dbpre'].'imgs';
        
        $rt1 = @mysql_query("CREATE TABLE IF NOT EXISTS `$admintable` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `username` varchar(50) NOT NULL,
          `userpass` varchar(50) NOT NULL,
          `create_time` int(11) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`)
        ) TYPE=MyISAM DEFAULT CHARACTER SET utf8;",$dbconn) or die(mysql_error());

        $rt2 = @mysql_query("CREATE TABLE IF NOT EXISTS `$albumstable` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(50) NOT NULL,
          `cover` int(11) NOT NULL DEFAULT '0',
          `create_time` int(11) NOT NULL DEFAULT '0',
          `private` tinyint(1) NOT NULL DEFAULT '0',
          `desc` text NOT NULL DEFAULT '',
          PRIMARY KEY (`id`),
          KEY `cover` (`cover`)
        ) TYPE=MyISAM DEFAULT CHARACTER SET utf8;",$dbconn) or die(mysql_error());

        $rt3 = @mysql_query("CREATE TABLE IF NOT EXISTS `$imgstable` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `album` smallint(4) NOT NULL,
          `name` varchar(100) NOT NULL,
          `dir` varchar(10) NOT NULL,
          `pickey` varchar(32) NOT NULL,
          `ext` varchar(10) NOT NULL,
          `status` tinyint(1) NOT NULL DEFAULT '0',
          `hits` int(11) NOT NULL DEFAULT '0',
          `create_time` int(11) NOT NULL DEFAULT '0',
          `private` tinyint(1) NOT NULL DEFAULT '0',
          `private_pass` varchar(50) NOT NULL DEFAULT '',
          `author` int(11) NOT NULL DEFAULT '0',
          `desc` text NOT NULL DEFAULT '',
          PRIMARY KEY (`id`),
          KEY `pickey` (`pickey`),
          KEY `imgalbum` (`album`)
        ) TYPE=MyISAM DEFAULT CHARACTER SET utf8;",$dbconn) or die(mysql_error());
        
        if(!$rt1 || !$rt2 || !$rt3){
            echo "创建表结构错误！";
            exit();
        }
        
        $rt4 = @mysql_query("REPLACE INTO `$admintable` (`id`, `username`, `userpass`, `create_time`) VALUES (1, '".$setting['username']."', '".md5($setting['userpass'])."','".time()."');",$dbconn);
        if(!$rt4){
            echo "添加用户数据错误！Error:".mysql_error();
            exit();
            
        }
        @mysql_query("REPLACE INTO `$albumstable` (`id`, `name`, `cover`,`create_time`) VALUES (1, '默认相册', '0','".time()."');",$dbconn);
        mysql_close($dbconn);
    }
    
    function creat_sqlite($dbname,$pre,$adminuser,$adminpass){
        $config_file_content = "<?php\n";
        $config_file_content .= "\$db_config = array(\n";
        $config_file_content .= "'adapter'  => 'sqlite',\n";
        $config_file_content .= "'dbname'   => '".$dbname."',\n";
        $config_file_content .= "'charset'  => 'utf8',\n";
        $config_file_content .= "'pre'      => '".$pre."'\n";
        $config_file_content .= ");\n";
        $config_file_content .= "?>";
        if(!@file_put_contents(ROOTDIR.'conf/config.php',$config_file_content)){
            echo "<script> alert('无法创建数据库配置文件！');history.back();</script>";
            exit();
        }
        @chmod(ROOTDIR.'conf/config.php',0755);
        
        $conn=sqlite_open($dbname);
        
        $admintable = $pre.'admin';
        $albumstable = $pre.'albums';
        $imgstable = $pre.'imgs';
        
        sqlite_query("CREATE TABLE '<?php' (a)",$conn);

        sqlite_query("CREATE TABLE $admintable (
                  id INTEGER NOT NULL PRIMARY KEY,
                  username varchar(50) NOT NULL,
                  userpass varchar(50) NOT NULL,
                  create_time int(11) NOT NULL DEFAULT '0'
                )",$conn);
        

        sqlite_query("CREATE TABLE $albumstable (
                  id INTEGER NOT NULL PRIMARY KEY,
                  name varchar(50) NOT NULL,
                  cover int(11) NOT NULL DEFAULT '0',
                  create_time int(11) NOT NULL DEFAULT '0',
                  private tinyint(1) NOT NULL DEFAULT '0',
                  desc text NOT NULL DEFAULT ''
                )",$conn);
        sqlite_query("CREATE INDEX cover on $albumstable (cover)",$conn);

        sqlite_query("CREATE TABLE $imgstable (
                  id INTEGER NOT NULL PRIMARY KEY,
                  album smallint(4) NOT NULL,
                  dir varchar(10) NOT NULL,
                  pickey varchar(32) NOT NULL,
                  ext varchar(10) NOT NULL,
                  name varchar(100) NOT NULL,
                  status tinyint(1) NOT NULL DEFAULT '0',
                  hits int(11) NOT NULL DEFAULT '0',
                  create_time int(11) NOT NULL DEFAULT '0',
                  private tinyint(1) NOT NULL DEFAULT '0',
                  private_pass varchar(50) NOT NULL DEFAULT '',
                  author int(11) NOT NULL DEFAULT '0',
                  desc text NOT NULL DEFAULT ''
                )",$conn);
        sqlite_query("CREATE INDEX pickey on $imgstable (pickey)",$conn);
        sqlite_query("CREATE INDEX imgalbum on $imgstable (album)",$conn);

        sqlite_query("INSERT INTO $admintable (id, username, userpass,create_time) VALUES (1, '".$adminuser."', '".md5($adminpass)."','".time()."')",$conn);
        sqlite_query("INSERT INTO $albumstable (id, name, cover ,create_time) VALUES (1, '默认相册', '0','".time()."')",$conn);
        sqlite_close($conn);
    }
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>美优相册管理系统 - 安装程序</title>
<style>
body{
    color:#444;
    font-size:12px;
    font-family:Arial;
    background:#F0F5F8;
}
a{
    text-decoration:none;
    color:#005EAC;
}
#main{
    width:760px;
    margin:10px auto;
    background:#fff;
    border:1px solid #eee;
    padding:15px;
}
h1{
    padding-bottom:10px;
    font-size:18px;
    border-bottom:1px solid #ddd;
}

ul.info{
    list-style:circle;
}
ul.info li{
    line-height:2;
}
ul.check {
    margin:10px 70px 0 10px;
    line-height:2;
}
ul.check li span{
    float:right;
}
.green{
    color:green;
}
.red{
    color:red;
}
.blue{
    color:#005EAC;
}
.orange{
    color:#ff9900;
}
input.btn{
    background-color:#005EAC;
    border-color:#B8D4E8 #124680 #124680 #B8D4E8;
    border-style:solid;
    border-width:1px;
    color:#FFFFFF;
    cursor:pointer;
    font-size:12px;
    height:23px;
    text-align:center;
}
table.setting{
    margin-left:40px;
}
table.setting th{
    width:150px;
    text-align:left;
}
</style>
<script>
function checkSubmit(o){
    if(o.dbadapter.value == 'mysql' || o.dbadapter.value == 'mysqli'){
        if(o.dbhost.value == ''){
            alert('数据库主机不能为空！');
            return false;
        }
    
        if(o.dbport.value == ''){
            alert('端口号不能为空！');
            return false;
        }
        if(o.dbhost.value == ''){
            alert('数据库主机不能为空！');
            return false;
        }
        if(o.dbuser.value == ''){
            alert('数据库用户名不能为空！');
            return false;
        }
        if(o.dbname.value == ''){
            alert('数据库名不能为空！');
            return false;
        }
    }else if(o.dbadapter.value == 'sqlite'){
        if(o.sqlitedbname.value == ''){
            alert('Sqlite数据库路径不能为空！');
            return false;
        }
    }else{
        alert('请选择数据库类型！');
        return false;
    }
    
    if(o.username.value == ''){
        alert('管理员用户名不能为空！');
        return false;
    }
    if(o.userpass.value == ''){
        alert('管理员密码不能为空！');
        return false;
    }
    
    if(o.passagain.value != o.userpass.value){
        alert('两次密码不一致！');
        return false;
    }
    
    if(o.url.value == ''){
        alert('站点URL不能为空!');
        return false;
    }
    o.submit();
}

function selected_adapter(val){
    if(val == 'sqlite'){
        document.getElementById('sqlite_div').style.display = '';
        document.getElementById('mysql_div').style.display = 'none';
        document.getElementById('dbauth_div').style.display = 'none';
        document.getElementById('pre_div').style.display = '';
    }else{
        document.getElementById('sqlite_div').style.display = 'none';
        document.getElementById('mysql_div').style.display = '';
        document.getElementById('dbauth_div').style.display = '';
        document.getElementById('pre_div').style.display = '';
    }
}
</script>
</head>
<body>
<div id="main">
<?php
if (PHP_VERSION >= "5.1.0") {
	date_default_timezone_set ( 'Asia/Shanghai' );
}
define('ROOTDIR',dirname(__FILE__).'/');
define('LIBDIR',ROOTDIR.'libs/');
$action = isset($_GET['step'])?$_GET['step']:'1';

if(file_exists(ROOTDIR.'conf/install.lock') && $action!=3){
?>
<h1 class="green">美优相册管理系统 已成功安装！</h1>
<ul class="info">
<li class="red">应用程序已经安装过！请勿重新安装！</li>
<li>若要重新安装，请删除conf/install.lock，然后重新执行此安装程序！</li>
</ul>
<?php
}else{
    if($action == '1'){
        $dir_error = false;
        echo "<h1 class=\"blue\">欢迎使用美优相册管理系统！ <span class=\"orange\">安装第一步：检查环境</span></h1>\n";
        echo "<h2>系统环境</h2>\n";
        echo "<ul class=\"check\">\n";
        foreach(get_system_info() as $v){
            echo "<li><span>".$v[1]."</span>".$v[0]."</li>\n";
        }
        echo "</ul>\n";
        echo "<h2>检测目录权限</h2>\n";
        echo "<ul class=\"check\">\n";
        if(is_dir(ROOTDIR.'conf') && is_writable(ROOTDIR.'conf')){
            echo "<li><span class=\"green\">可写</span> conf</li>\n";
        }else{
            $dir_error = true;
            echo "<li><span class=\"red\">不可写</span> conf</li>\n";
        }
        
        if(is_dir(ROOTDIR.'data') && is_writable(ROOTDIR.'data')){
            echo "<li><span class=\"green\">可写</span> data</li>\n";
        }else{
            $dir_error = true;
            echo "<li><span class=\"red\">不可写</span> data</li>\n";
        }
        
        echo "</ul>\n";
        if(!get_gd_version() || $dir_error){
            echo "<div align=\"center\" class=\"red\">检查到您的配置有问题，无法进行下一步安装</div>";
        }else{
            echo "<div align=\"center\"><input type=\"button\" onclick=\"window.location.href='install.php?step=2'\" value=\"下一步\" class=\"btn\" /></div>";
        }
    }elseif($action == '2'){
 ?>
    <h1 class="blue">欢迎使用美优相册管理系统！ <span class="orange">安装第二步：配置系统</span></h1>
<h2>数据库配置</h2>

<form action="install.php?step=install" method="post" onsubmit="checkSubmit(this);return false;"><table class="setting">
<tbody>
<tr><th>数据库类型</th><td>
    <select id="sel_dbadapter" name="dbadapter" onchange="selected_adapter(this.value)">
    <?php 
    if(function_exists('mysql_connect')){
        echo '<option value="mysql">Mysql</option>';
    }

    if(function_exists('mysqli_connect')){
        echo '<option value="mysqli">Mysqli</option>';
    }

    if(function_exists('sqlite_open')){
        echo '<option value="sqlite">Sqlite</option>';
    }
    ?>
    </select></td>
</tbody>
<tbody id="mysql_div">
<tr><th>数据库主机</th><td><input name="dbhost" type="text" value="localhost" /></td>
<tr><th>端口号</th><td><input name="dbport" type="text" value="3306" /></td>
</tbody>
<tbody id="dbauth_div">
<tr><th>用户名</th><td><input name="dbuser" type="text" value="root" /></td>
<tr><th>密码</th><td><input name="dbpass" type="password" value="" /></td>
<tr><th>数据库名</th><td><input name="dbname" type="text" value="meiupic" /> <input type="checkbox" name="create_db" value="1" checked="checked" /> 自动创建数据库</td>
</tbody>
<tbody id="pre_div">
<tr><th>表前缀</th><td><input name="dbpre" type="text" value="meu_" /></td>
</tbody>
<tbody id="sqlite_div" style="display:none;">

<tr><th>数据库路径</th><td><input name="sqlitedbname" type="text" value="data/database.php" /></td>
</tbody>
</table>
<h2>管理员帐号</h2>
<table class="setting">
<tr><th>登录名</th><td><input name="username" type="text" value="admin" /></td>
<tr><th>登录密码</th><td><input name="userpass" type="password" value="" /></td>
<tr><th>密码确认</th><td><input name="passagain" type="password" value="" /></td>
</table>
<h2>其他设置</h2>
<table class="setting">

<tr><th>站点URL</th><td><input name="url" size="40" type="text" value="<?php echo 'http://'.$_SERVER['SERVER_NAME'].get_basepath(); ?>" /> 请带上URL末端的"/" </td>
</table>
<div align="left" style="margin-top:10px;padding-left:200px"><input type="button" onclick="window.location.href='install.php?step=1'" value="上一步" class="btn" />&nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" value="立即安装" class="btn" /></div></form>
<script>selected_adapter(document.getElementById("sel_dbadapter").value);</script>

 <?php
    }elseif($action == 'install'){
        $setting['dbhost'] = trim($_POST['dbhost']);
        $setting['dbport'] = trim($_POST['dbport']);
        $setting['dbuser'] = trim($_POST['dbuser']);
        $setting['dbpass'] = $_POST['dbpass'];
        $setting['dbname'] = trim($_POST['dbname']);
        $setting['dbadapter'] = trim($_POST['dbadapter']);
        $setting['sqlitedbname'] = trim($_POST['sqlitedbname']);
        $setting['dbpath'] = trim($_POST['dbpath']);
        $setting['dbpre'] = trim($_POST['dbpre']);
        $setting['url'] = trim($_POST['url']);
        $setting['username'] = trim($_POST['username']);
        $setting['userpass'] = trim($_POST['userpass']);
        
        if($setting['dbadapter'] == 'sqlite'){
            if(file_exists($setting['sqlitedbname'])){
                echo "<script> alert('Sqlite数据库已经存在，无法继续安装！');history.back();</script>";
                exit();
            }
            creat_sqlite($setting['sqlitedbname'],$setting['dbpre'],$setting['username'],$setting['userpass']);
        }else{
            creat_mysql($setting);
        }
        $setting_content = "<?php \n";
        $setting_content .= "\$setting['site_title'] = '我的相册';\n";
        $setting_content .= "\$setting['site_keyword'] = '';\n";
        $setting_content .= "\$setting['site_description'] = '';\n";
        $setting_content .= "\$setting['url'] = '".$setting['url']."';\n";
        $setting_content .= "\$setting['open_pre_resize'] = false;\n";
        $setting_content .= "\$setting['resize_img_width'] = '1600';\n";
        $setting_content .= "\$setting['resize_img_height'] = '1200';\n";
        $setting_content .= "\$setting['resize_quality'] = '100';\n";
        $setting_content .= "\$setting['demand_resize'] = false;\n";
        $setting_content .= "\$setting['imgdir_type'] = '2';\n";
        $setting_content .= "\$setting['size_allow'] = '1024000';\n";
        $setting_content .= "\$setting['pageset'] = '15';\n";
        $setting_content .= "\$setting['open_photo'] = true;\n";
        $setting_content .= "\$setting['gallery_limit'] = '60';\n";
        $setting_content .= "\$setting['access_ctl'] = false;\n";
        $setting_content .= "\$setting['access_domain'] = '".$_SERVER['SERVER_NAME']."';\n";
        $setting_content .= "\$setting['open_watermark'] = false;\n";
        $setting_content .= "\$setting['watermark_path'] = '';\n";
        $setting_content .= "\$setting['watermark_pos'] = 0;\n";
        $setting_content .= "?>";
        if(!@file_put_contents(ROOTDIR.'conf/setting.php',$setting_content)){
            echo "<script> alert('无法创建基本配置文件！');history.back();</script>";
            exit();
        }
        @chmod(ROOTDIR.'conf/setting.php',0755);
        
        @file_put_contents(ROOTDIR.'conf/install.lock',date('Y-m-d H:i:s').' Installed!');
        @chmod(ROOTDIR.'conf/install.lock',0755);
        
        echo "安装成功...正在跳转  <script>window.location.href='install.php?step=3';</script>";

    }elseif($action == '3'){
?>
    <h1 class="green">恭喜您！ 美优相册管理系统 已成功安装！</h1>
    <ul class="info">
    <li class="red">请记得删除install.php文件！</li>
    <li><a href="admin.php">开始登录相册上传图片吧！</a></li>
    </ul>
<?php
    }
}
?>
</div>
</body>
</html>