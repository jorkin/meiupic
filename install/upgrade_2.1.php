<?php
if(!defined('IN_MEIU')) exit('Access Denied');

$sqls = array();
if($db->adapter == 'sqlite'){
    $sqls[] = "ALTER TABLE #@photos ADD cate_id INT NOT NULL DEFAULT 0";
    $sqls[] = "CREATE INDEX p_cate_id on #@photos (cate_id)";
}else{
    $sqls[] = "ALTER TABLE `meu_photos` ADD `cate_id` INT NOT NULL DEFAULT '0' AFTER `album_id` ,ADD INDEX ( `cate_id` );"
}

//$sqls[] = $db->insert('#@nav',array('type'=>0,'name'=>lang('album_index'),'url' =>'default','sort'=>'100'));
//$sqls[] = $db->insert('#@nav',array('type'=>0,'name'=>lang('tags'),'url' =>'tags','sort'=>'100'));
//$sqls[] = $db->insert('#@nav',array('type'=>0,'name'=>lang('category'),'url' =>'category','sort'=>'100'));

foreach($sqls as $sql){
    $db->query($sql);
}

//require_once(ROOTDIR.'install/upgrade_2.2.php');