<?php

if (PHP_VERSION >= "5.1.0") {
	date_default_timezone_set ( 'Asia/Shanghai' );
}

if(!file_exists('conf/config.php') || !file_exists('conf/setting.php')){
    header('Location: ./install.php');
    exit;
}
Define('FCPATH',__FILE__);
Define('ROOTDIR',dirname(FCPATH).'/');

require_once('conf/setting.php');
require_once('conf/config.php');
define('COREDIR',ROOTDIR.'core/');
Define('LIBDIR',COREDIR.'libs/');
Define('CTLDIR',COREDIR.'ctls/');
Define('VIEWDIR',COREDIR.'views/');
Define('MODELDIR',COREDIR.'models/');

Define('DATADIR',ROOTDIR.$setting['imgdir'].'/');

Define('SITE_URL',$setting['url']);
Define('PAGE_SET',$setting['pageset']);

require_once(LIBDIR.'func.php');

run();