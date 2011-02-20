<?php
/**
 * $Id:index.php 93 2010-09-07 15:35:32Z lingter $
 * 
 * @author : Lingter
 * @support : http://meiupic.meiu.cn
 * @copyright : (c)2011 meiu.cn lingter@gmail.com
 */
error_reporting(E_ALL);
define('LANGSET','zh_cn');

define('FCPATH',__FILE__);
define('ROOTDIR',dirname(FCPATH).'/');
define('COREDIR',ROOTDIR.'core/');
define('LIBDIR',COREDIR.'libs/');
define('INCDIR',COREDIR.'include/');
define('CTLDIR',COREDIR.'ctls/');
define('VIEWDIR',COREDIR.'views/');
define('MODELDIR',COREDIR.'models/');
define('DATADIR',ROOTDIR.'data/');
define('PLUGINDIR',ROOTDIR.'plugins/');

if(!file_exists(ROOTDIR.'conf/config.php')){
    header('Location: ./install/');
    exit;
}

require_once(INCDIR.'bootstrap.inc.php');
meiu_bootstrap();