<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

if (PHP_VERSION >= "5.1.0") {
	date_default_timezone_set ( 'Asia/Shanghai' );
}

if(!file_exists('conf/config.php') || !file_exists('conf/setting.php')){
    header('Location: ./install.php');
    exit;
}
define('FCPATH',__FILE__);
define('ROOTDIR',dirname(FCPATH).'/');

require_once('conf/setting.php');
require_once('conf/config.php');
define('COREDIR',ROOTDIR.'core/');
define('LIBDIR',COREDIR.'libs/');
define('CTLDIR',COREDIR.'ctls/');
define('VIEWDIR',COREDIR.'views/');
define('MODELDIR',COREDIR.'models/');

define('DATADIR',ROOTDIR.$setting['imgdir'].'/');

define('SITE_URL',$setting['url']);
define('PAGE_SET',$setting['pageset']);

require_once(LIBDIR.'func.php');

run();