<?php
set_time_limit(0);
ob_end_clean();

header("Content-type: text/html; charset=utf-8");

function mkImgLink($dir,$key,$ext,$size='big'){
    if($size=='orig'){
        return 'data/'.$dir.'/'.$key.'.'.$ext;
    }
    return 'data/'.$dir.'/'.$key.'_'.$size.'.'.$ext;
}

function resizeImg($dir,$key,$ext){
    include_once 'core/libs/image.class.php';
    $imgobj = new Image();
    $imgobj->load(ROOTDIR.mkImgLink($dir,$key,$ext,'orig'));
    $imgobj->setQuality(95);

    $size = 'big';
    $width = '900';
    $height = '900';
    $bigpath = ROOTDIR.mkImgLink($dir,$key,$ext,$size);
    $imgobj->resizeScale($width,$height );
    $imgobj->save($bigpath);
    @chmod($bigpath,0755);

    $imgobj->load($bigpath);

    $size = 'medium';
    $width = '550';
    $height = '550';
    $newpath = ROOTDIR.mkImgLink($dir,$key,$ext,$size);
    $imgobj->resizeScale($width,$height );
    $imgobj->save($newpath);
    @chmod($newpath,0755);

    $imgobj->load($bigpath);

    $size = 'small';
    $width = '240';
    $height = '240';
    $newpath = ROOTDIR.mkImgLink($dir,$key,$ext,$size);
    $imgobj->resizeScale($width,$height );
    $imgobj->save($newpath);
    @chmod($newpath,0755);

    //$imgobj = new Image();
    $imgobj->load($bigpath);

    $size = 'thumb';
    $width = '110';
    $height = '150';
    $newpath = ROOTDIR.mkImgLink($dir,$key,$ext,$size);
    $imgobj->resizeScale($width,$height );
    $imgobj->save($newpath);
    @chmod($newpath,0755);

    //$imgobj = new Image();
    $imgobj->load($bigpath);

    $size = 'square';
    $width = '75';
    $newpath = ROOTDIR.mkImgLink($dir,$key,$ext,$size);
    $imgobj->square($width);
    $imgobj->save($newpath);
    @chmod($newpath,0755);
}
require './conf/config.php';

define('ROOTDIR','./');
if($db_config['adapter'] == 'sqlite'){
    $conn=sqlite_open('data/database.db');
    $dbconn = sqlite_open($db_config['dbname']);
    
    $admintable = $db_config['pre'].'admin';
    $albumstable = $db_config['pre'].'albums';
    $imgstable = $db_config['pre'].'imgs';
    echo "重新创建数据库 ...... ";
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
    
    echo "成功<br />\n";
    flush();
    
    echo "迁移管理员数据 ...... <br />\n";
    $query = sqlite_query("select * from $admintable",$dbconn);
    while($row = sqlite_fetch_array($query)){
        $rt = sqlite_query("insert into $admintable values ('".$row['id']."','".$row['username']."','".$row['userpass']."','".time()."')",$conn);
        if($rt){
            echo "管理员 ".$row['username']." 成功 <br />\n";
        }else{
            echo "管理员 ".$row['username']." 失败 <br />\n";
        }
        flush();
    }
    echo "迁移相册数据 ...... <br />\n";
    $query = sqlite_query("select * from $albumstable",$dbconn);
    while($row = sqlite_fetch_array($query)){
        if($row['cover']){
            $query1 = sqlite_query("select id from $imgstable where thumb='".$row['cover']."'",$dbconn);
            $cover_row = sqlite_fetch_array($query1);
            $cover_id = $cover_row['id'];
        }else{
            $cover_id = 0;
        }

        $rt = sqlite_query("insert into $albumstable(id,name,cover,create_time) values ('".$row['id']."','".$row['name']."','".$cover_id."','".time()."')",$conn);
        if($rt){
            echo "相册 ".$row['name']." 成功 <br />\n";
        }else{
            echo "相册 ".$row['name']." 失败 <br />\n";
        }
        flush();
    }
    echo "迁移图片数据 ...... <br />\n";
    $query = sqlite_query("select * from $imgstable",$dbconn);
    while($row = sqlite_fetch_array($query)){
        preg_match("/(.*)\/(.*)\.(jpg|jpeg|gif|png)$/",$row['path'],$matches);
        $rt = sqlite_query("insert into $imgstable(id,album,dir,pickey,ext,name,status,create_time) values (".$row['id'].",".$row['album'].",'".$matches[1]."','".$matches[2]."','".$matches[3]."','".$row['name']."','".$row['status']."',".time().")",$conn);
        if($rt){
            $dir = $matches[1];
            $key = $matches[2];
            $ext = $matches[3];
            
            resizeImg($dir,$key,$ext);
            
            echo "照片 ".$row['name']." 成功 <br />\n";
        }else{
            echo "照片 ".$row['name']." 失败 <br />\n";
        }
        flush();
    }
    echo "修改数据库配置文件 ...... ";
    $config_file_content = "<?php\n";
    $config_file_content .= "\$db_config = array(\n";
    $config_file_content .= "'adapter'  => 'sqlite',\n";
    $config_file_content .= "'dbname'   => 'data/database.db',\n";
    $config_file_content .= "'charset'  => 'utf8',\n";
    $config_file_content .= "'pre'      => '".$db_config['pre']."'\n";
    $config_file_content .= ");\n";
    $config_file_content .= "?>";

    if(!@file_put_contents(ROOTDIR.'conf/config.php',$config_file_content)){
        echo "失败<br />\n";
    }else{
        echo "成功<br />\n";
        @chmod(ROOTDIR.'conf/config.php',0755);
    }
    echo "数据库已从".$db_config['dbname']."迁移至data/database.db,如果升级成功请手动删除".$db_config['dbname']."<br />\n";
}elseif($db_config['adapter'] == 'txtsql'){
    echo "新版中取消了txtSQL支持，无法升级！<br />\n";
}else{
    $dbconn = @mysql_connect($db_config['dbhost'].':'.$db_config['dbport'],$db_config['dbuser'],$db_config['dbpass'],true);
    @mysql_select_db($db_config['dbname'],$dbconn);
    mysql_query('SET NAMES "utf8"',$dbconn);
    
    $admintable = $db_config['pre'].'admin';
    $albumstable = $db_config['pre'].'albums';
    $imgstable = $db_config['pre'].'imgs';

    echo "修改管理员表`$admintable` ...... ";
    $rt = mysql_query("ALTER TABLE `$admintable` ADD `create_time` INT NOT NULL DEFAULT '0' AFTER `userpass`;",$dbconn);
    if($rt){
        mysql_query("UPDATE `$admintable` set create_time=".time().";",$dbconn);
        echo "成功 <br />\n";
    }else{
        echo "失败 <br />\n";
    }
    flush();
    
    echo "修改`".$db_config['pre']."ablums`的表结构 ..... ";

    $rt = mysql_query("ALTER TABLE `$albumstable` CHANGE `cover` `cover_bak` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD `cover` INT(11) NOT NULL DEFAULT '0' AFTER `name` ,
    ADD `create_time` INT(11) NOT NULL DEFAULT '0' AFTER `cover` ,
    ADD `private` TINYINT(1) NOT NULL DEFAULT '0' AFTER `create_time` ,
    ADD `desc` TEXT NOT NULL AFTER `private`, ADD INDEX ( `cover` )",$dbconn);
    if($rt){
        echo "成功 <br />\n";
    }else{
        echo "失败 <br />\n";
    }
    flush();

    echo "迁移相册数据......<br />\n";
    $query = mysql_query("select * from `$albumstable`",$dbconn);
    while($row = mysql_fetch_array($query)){
        if($row['cover_bak']){
            $query1 = mysql_query("select id from `$imgstable` where thumb='".$row['cover_bak']."'",$dbconn);
            $cover_id = @mysql_result($query1,0);
        }else{
            $cover_id = 0;
        }
        $rt = mysql_query("update `$albumstable` set cover='".intval($cover_id)."',create_time=".time()." where id=".intval($row['id']),$dbconn);
        if($rt){
            echo "相册 ".$row['name']." 成功 <br />\n";
        }else{
            echo "相册 ".$row['name']." 失败 <br />\n";
        }
        flush();
    }
     $rt = mysql_query("ALTER TABLE `$albumstable` DROP `cover_bak`",$dbconn);
     if($rt){
         echo "迁移数据成功 <br />\n";
     }else{
         echo "迁移数据失败 <br />\n";
     }
     flush();
     echo "修改`$imgstable`的表结构 ..... ";
     $rt = mysql_query("ALTER TABLE `$imgstable` ADD `dir` VARCHAR( 10 ) NOT NULL AFTER `name` ,
     ADD `pickey` VARCHAR( 32 ) NOT NULL AFTER `dir` ,
     ADD `ext` VARCHAR( 10 ) NOT NULL AFTER `pickey` ,
     ADD `hits` INT( 11 ) NOT NULL DEFAULT '0' AFTER `ext` ,
     ADD `create_time` INT( 11 ) NOT NULL DEFAULT '0' AFTER `hits` ,
     ADD `private` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `create_time` ,
     ADD `private_pass` VARCHAR( 50 ) NOT NULL AFTER `private` ,
     ADD `author` INT( 11 ) NOT NULL DEFAULT '0' AFTER `private_pass` ,
     ADD `desc` TEXT NOT NULL AFTER `author`, ADD INDEX ( `album` ), ADD INDEX ( `pickey` );");
     if($rt){
         echo "成功 <br />\n";
     }else{
         echo "失败 <br />\n";
     }
     flush();
     
     
     echo "迁移图片数据......<br />\n";
     $query = mysql_query("select * from `$imgstable`",$dbconn);
     while($row = mysql_fetch_array($query)){
        preg_match("/(.*)\/(.*)\.(jpg|jpeg|gif|png)$/",$row['path'],$matches);
        $rt = mysql_query("update `$imgstable` set `dir`='".$matches[1]."',`pickey`='".$matches[2]."',`ext`='".$matches[3]."',create_time=".time()." where id='".$row['id']."'",$dbconn);
        if($rt){
            $dir = $matches[1];
            $key = $matches[2];
            $ext = $matches[3];
            
            resizeImg($dir,$key,$ext);
            
            echo "照片 ".$row['name']." 成功 <br />\n";
        }else{
            echo "照片 ".$row['name']." 失败 <br />\n";
        }
        flush();
     }
     $rt = mysql_query("ALTER TABLE `$imgstable` DROP `path`, DROP `thumb`;");
     if($rt){
          echo "迁移数据成功 <br />\n";
      }else{
          echo "迁移数据失败 <br />\n";
      }
      flush();
      
}

echo "重新配置基本设置 ...... ";
include('conf/setting.php');

$setting_content = "<?php \n";
$setting_content .= "\$setting['site_title'] = '我的相册';\n";
$setting_content .= "\$setting['site_keyword'] = '';\n";
$setting_content .= "\$setting['site_description'] = '';\n";
$setting_content .= "\$setting['url'] = '".$setting['url']."';\n";
if($setting['open_pre_resize']){
    $setting_content .= "\$setting['open_pre_resize'] = true;\n";
}else{
    $setting_content .= "\$setting['open_pre_resize'] = false;\n";
}
$setting_content .= "\$setting['resize_img_width'] = '".$setting['resize_img_width']."';\n";
$setting_content .= "\$setting['resize_img_height'] = '".$setting['resize_img_height']."';\n";
$setting_content .= "\$setting['resize_quality'] = '".$setting['resize_quality']."';\n";
$setting_content .= "\$setting['demand_resize'] = false;\n";
$setting_content .= "\$setting['imgdir_type'] = '2';\n";
$setting_content .= "\$setting['size_allow'] = '1024000';\n";
$setting_content .= "\$setting['pageset'] = '15';\n";
$setting_content .= "\$setting['open_photo'] = true;\n";
$setting_content .= "\$setting['gallery_limit'] = '60';\n";
$setting_content .= "\$setting['access_ctl'] = false;\n";
$setting_content .= "\$setting['access_domain'] = '".$_SERVER['SERVER_NAME']."';\n";
$setting_content .= "?>";

if(!@file_put_contents(ROOTDIR.'conf/setting.php',$setting_content)){
    echo "失败 <br />\n";
}else{
    echo "成功 <br />\n";
    @chmod(ROOTDIR.'conf/setting.php',0755);
}

echo "升级完毕！请删除update.php文件！";
?>