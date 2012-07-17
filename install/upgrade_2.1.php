<?php
if(!defined('IN_MEIU')) exit('Access Denied');

$sqls = array();
if($db->adapter == 'sqlite'){
    $sqls[] = "ALTER TABLE #@photos ADD cate_id INT NOT NULL DEFAULT 0";
    $sqls[] = "CREATE INDEX p_cate_id on #@photos (cate_id)";
}else{
    //把album字段加大
    $sqls[] = "ALTER TABLE #@albums CHANGE `name` `name` VARCHAR(150) CHARACTER SET utf8 NOT NULL";
    $sqls[] = "ALTER TABLE `meu_photos` ADD `cate_id` INT NOT NULL DEFAULT '0' AFTER `album_id` ,ADD INDEX ( `cate_id` );";
}

foreach($sqls as $sql){
    $db->query($sql);
}

//更新图片所属的分类
$album_mdl =& loader::model('album');
$photo_mdl =& loader::model('photo');
$albums = $album_mdl->get_all();
if($albums as $album){
    $photo_mdl->update_by_aid(array('cate_id'=>$album['cate_id']),$album['id']);
}

//setting新增值
$setting_mdl->set_conf('system.enable_comment_captcha',true);
$setting_mdl->set_conf('system.comment_audit',0);

$setting_mdl->set_conf('upload.enable_cut_big_pic',false);
$setting_mdl->set_conf('upload.max_width',1600);
$setting_mdl->set_conf('upload.max_height',1200);
$setting_mdl->set_conf('upload.enable_thumb_square',false);
$setting_mdl->set_conf('upload.thumb_width',180);
$setting_mdl->set_conf('upload.thumb_height',180);
$setting_mdl->set_conf('upload.use_old_imgname',false);
$setting_mdl->set_conf('display.album_pageset',12);
$setting_mdl->set_conf('display.photo_pageset',12);
$setting_mdl->set_conf('display.album_sort_default','ct_desc');
$setting_mdl->set_conf('display.photo_sort_default','tu_desc');

//require_once(ROOTDIR.'install/upgrade_2.2.php');