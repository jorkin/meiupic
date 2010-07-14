<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */
error_reporting(E_ERROR|E_WARNING|E_NOTICE);

if (PHP_VERSION >= "5.1.0") {
	date_default_timezone_set ( 'Asia/Shanghai' );
}

if(!file_exists('conf/config.php') || !file_exists('conf/setting.php')){
    header('Location: ./install.php');
    exit;
}

header("Content-type: text/html; charset=utf-8");

define('Version','1.0');

define('FCPATH',__FILE__);
define('ROOTDIR',dirname(FCPATH).'/');

require_once('conf/setting.php');
require_once('conf/config.php');
define('COREDIR',ROOTDIR.'core/');
define('LIBDIR',COREDIR.'libs/');
define('INCDIR',COREDIR.'include/');
define('CTLDIR',COREDIR.'ctls/');
define('VIEWDIR',COREDIR.'views/');
define('MODELDIR',COREDIR.'models/');

define('DATADIR',ROOTDIR.'data/');

define('SITE_URL',$setting['url']);
define('PAGE_SET',$setting['pageset']);

require_once(INCDIR.'base.php');
require_once(INCDIR.'func.php');
run_admin();