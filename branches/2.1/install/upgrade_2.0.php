<?php
if(!defined('IN_MEIU')) exit('Access Denied');

$sqls = array();
if($this->db->adapter == 'sqlite'){
    $sqls[] = 'CREATE TABLE #@cate (
        id integer NOT NULL primary key,
        par_id int(4) NOT NULL DEFAULT 0,
        name varchar(100) NOT NULL,
        cate_path varchar(255) DEFAULT NULL,
        sort int(4) NOT NULL DEFAULT 0)';
    $sqls[] = 'CREATE TABLE #@nav (
        id integer NOT NULL primary key ,
        type tinyint(1) NOT NULL DEFAULT 1,
        name varchar(50) NOT NULL ,
        url varchar(200) NOT NULL ,
        sort smallint(4) NOT NULL  DEFAULT 100,
        enable tinyint(1) NOT NULL DEFAULT 1)';
    $sqls[] = "ALTER TABLE #@albums ADD cate_id int(4) NOT NULL DEFAULT 0";
    $sqls[] = "CREATE INDEX cg_par_id on #@cate (par_id)";
    $sqls[] = "CREATE INDEX a_cate_id on #@albums (cate_id)";
    $sqls[] = "CREATE INDEX p_album_id on #@photos (album_id)";
}else{
    $sqls[] = $this->_createtable("CREATE TABLE `#@cate` (
          `id` int(4) NOT NULL AUTO_INCREMENT,
          `par_id` int(4) NOT NULL DEFAULT '0',
          `name` varchar(100) NOT NULL,
          `cate_path` varchar(255) DEFAULT NULL,
          `sort` int(4) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `par_id` (`par_id`)
        ) TYPE=MyISAM ;");
    $sqls[] = $this->_createtable("CREATE TABLE `#@nav` (
        `id` smallint(4) NOT NULL AUTO_INCREMENT ,
        `type` tinyint(1) NOT NULL DEFAULT '1',
        `name` varchar(50) NOT NULL ,
        `url` varchar(200) NOT NULL ,
        `sort` smallint(4) NOT NULL DEFAULT '100',
        `enable` tinyint(1) NOT NULL DEFAULT '1',
        PRIMARY KEY ( `id` )
        ) TYPE=MyISAM ;");
    $sqls[] = 'ALTER TABLE `#@albums` ADD `cate_id` int(4) NOT NULL DEFAULT 0 , ADD INDEX `cate_id` (`cate_id`)';
}

$sqls[] = $this->db->insert('#@nav',array('type'=>0,'name'=>lang('album_index'),'url' =>'default','sort'=>'100'));
$sqls[] = $this->db->insert('#@nav',array('type'=>0,'name'=>lang('tags'),'url' =>'tags','sort'=>'100'));
$sqls[] = $this->db->insert('#@nav',array('type'=>0,'name'=>lang('category'),'url' =>'category','sort'=>'100'));

foreach($sqls as $sql){
    $this->db->query($sql);
}
$this->setting->set_conf('site.share_title',lang('share_title'));
//重新设置
$this->plugin->remove_plugin('copyimg');
$this->plugin->install_plugin('copyimg');
$this->plugin->enable_plugin('copyimg');

$config =& loader::config();
$default_config =& loader::config('config.default');

save_config_file(ROOTDIR.'conf/config.php', $config, $default_config);

//require_once(ROOTDIR.'install/upgrade_2.1.0.php');