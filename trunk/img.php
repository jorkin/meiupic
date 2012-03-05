<?php

define('FCPATH',__FILE__);
define('ROOTDIR',dirname(FCPATH).DIRECTORY_SEPARATOR);

define('COREDIR',ROOTDIR.'core'.DIRECTORY_SEPARATOR);
define('LIBDIR',COREDIR.'libs'.DIRECTORY_SEPARATOR);
define('INCDIR',COREDIR.'include'.DIRECTORY_SEPARATOR);
define('CTLDIR',COREDIR.'ctls'.DIRECTORY_SEPARATOR);
define('VIEWDIR',COREDIR.'views'.DIRECTORY_SEPARATOR);
define('MODELDIR',COREDIR.'models'.DIRECTORY_SEPARATOR);
define('DATADIR',ROOTDIR.'data'.DIRECTORY_SEPARATOR);
define('PLUGINDIR',ROOTDIR.'plugins'.DIRECTORY_SEPARATOR);
define('MAGIC_GPC',get_magic_quotes_gpc());

if (floor(PHP_VERSION) < 5){
    define('PHPVer',4);
}else{
    define('PHPVer',5);
}
date_default_timezone_set('UTC');
require_once(COREDIR.'loader.php');
require_once(INCDIR.'functions.php');

$config = loader::config();
$path = getGet('path');

$realpath = get_realpath(ROOTDIR.$path);

if(stripos($realpath, ROOTDIR) !== 0){
    //非法的路径
    exit;
}

if(!file_exists($realpath)){
    header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found!');
    exit;
}

function tryBrowserCache(){
    if(!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) ){
        $mtime = filemtime($realpath);
        $iftime = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
        
        if($iftime < 1){
            return false;
        }
        if($iftime < $mtime){
            return false;
        } else {
            header ($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
            return true;
        }
    }
    return false;
}

if(tryBrowserCache()){
    exit();
}

$imglib =& loader::lib('image');
$imglib->load($realpath);

$gmdate_expires = gmdate ('D, d M Y H:i:s', strtotime ('now +10 days')) . ' GMT';
$gmdate_modified = gmdate ('D, d M Y H:i:s') . ' GMT';
$interval = 60*60*6;

header ('Last-Modified: ' .$gmdate_modified);
header ('Accept-Ranges: none');
header ("Expires: " . $gmdate_expires);
header ("Cache-Control: max-age=$interval, must-revalidate");

$w = getGet('w');
$imglib->resizeTo($w);
$imglib->output();