<?php

if (PHP_VERSION >= "5.1.0") {
	date_default_timezone_set ( 'Asia/Shanghai' );
}

if(!file_exists('conf/config.php') || !file_exists('conf/setting.php')){
    header('Location: ./install.php');
    exit;
}
require_once('conf/setting.php');
require_once('conf/config.php');


Define('FCPATH',__FILE__);
Define('ROOTDIR',dirname(FCPATH).'/');
Define('LIBDIR',ROOTDIR.'libs/');
Define('CTLDIR',ROOTDIR.'ctls/');
Define('VIEWDIR',ROOTDIR.'views/');
Define('DATADIR',ROOTDIR.$setting['imgdir'].'/');

Define('SITE_URL',$setting['url']);
Define('PAGE_SET',$setting['pageset']);

require_once(LIBDIR.'func.php');

run();