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

require_once(COREDIR.'loader.php');
require_once(INCDIR.'plugin.php');
require_once(INCDIR.'functions.php');

$config = loader::config();
$path = getGet('path');

$realpath = authcode($path, $operation = 'DECODE', $config['img_path_key']);
if(!file_exists($realpath)){
    header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found!');
    exit;
}

function tryBrowserCache(){
    //echo $_SERVER['HTTP_IF_MODIFIED_SINCE'];exit;
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

$w = getGet('w');

$imglib =& loader::lib('image');
$imglib->load($realpath);

$width = $imglib->getWidth();
$height = $imglib->getHeight();

$max = time();
$interval = 10*86400; // 10 day

header ('Last-Modified: ' .gmdate ("r", $max));
header ("Expires: " . gmdate ("r", ($max + $interval)));
header ("Cache-Control: max-age=$interval");

$imglib->resizeTo($w);
$imglib->output();