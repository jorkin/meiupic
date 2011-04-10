<?php

function site_link($ctl='default',$act='index',$pars=array()){
    $uri =& loader::lib('uri');
    return $uri->mk_uri($ctl,$act,$pars);
}

function no_cache_header(){
    header("Content-Type:text/xml;charset=utf-8");
    header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
}

function ajax_box_success($content , $title = '', $close_time = 0 , $forward = ''){
    no_cache_header();
    
    if(!$title){
        $title = lang('system_notice');
    }
    echo loader::lib('json')->encode(
        array('ret'=>true,'html'=>ajax_box($content,$title,$close_time,$forward))
    );
    exit;
}

function ajax_box_failed($info){
    no_cache_header();
    
    echo loader::lib('json')->encode(
        array('ret'=>false,'html'=>$info)
    );
    exit;
}

function ajax_box( $content , $title = '', $close_time = 0 , $forward = '' )
{   
    if(!$title){
        $title = lang('system_notice');
    }
    $_config = $GLOBALS['THEME_CONFIG'];
    ob_start();
    include template('block/ajax_box');
    $page_content = ob_get_clean();
    return $page_content;
}

function enum_priv_type($v){
    switch($v){
        case 0:
        return lang('album_type_public');
        case 1:
        return lang('album_type_passwd');
        case 2:
        return lang('album_type_ques');
        case 3:
        return lang('album_type_private');
    }
}

function get_sort_list($setting,$type,$default){
    $str = '<div class="listorder f_right selectlist">
    <span class="label">排序:</span>';
    $str .= '<div class="selected"></div><ul class="optlist">';
    $sort = isset($_COOKIE['_sortset_'.$type])?$_COOKIE['_sortset_'.$type]:$default;
    foreach($setting as $k=>$v){
        if($v.'_asc' == $sort){
            $str .= '<li class="current"><a href="javascript:void(0);" onclick="sort_setting(\''.$type.'\',\''.$v.'_desc\');" class="list_asc_on"><span>'.$k.'</span></a>';
        }elseif($v.'_desc' == $sort){
            $str .= '<li class="current"><a href="javascript:void(0);" onclick="sort_setting(\''.$type.'\',\''.$v.'_asc\');" class="list_desc_on"><span>'.$k.'</span></a>';
        }else{
            $str .= '<li><a href="javascript:void(0);" onclick="sort_setting(\''.$type.'\',\''.$v.'_asc\');" class="list_asc"><span>'.$k.'</span></a>';
        }
    }
    $str = $str.'</ul></div>';
    return array($sort,$str);
}

function get_page_setting($type){
    $arr = array(12,30,56);
    $current = isset($_COOKIE['_pageset_'.$type])?$_COOKIE['_pageset_'.$type]:'12';
    
    $str = '<div class="pset f_right selectlist">
        <span class="label">显示数:</span>';
    $str .= '<div class="selected"></div><ul class="optlist">';
    foreach($arr as $v){
        $str .= '<li '.($current==$v?'class="current"':'').'><a href="javascript:void(0);" onclick="page_setting(\''.$type.'\','.$v.');">'.$v.'</a></li>';
    }
    $str .= '</ul></div>';
    return array($current,$str);
}

function template($file) {
    if(strpos($file,':')!==false ) {
        list($templateid, $file) = explode(':', $file);
        $tpldir = 'plugins/'.$templateid.'/templates';
    }
    $tpldir = isset($tpldir)?$tpldir:TPLDIR;
    $templateid = isset($templateid) ? $templateid : TEMPLATEID;
    
    $tplfile = ROOTDIR.$tpldir.'/'.$file.'.htm';
    
    if(TEMPLATEID != 1 && !file_exists($tplfile)) {
        $tplfile = ROOTDIR.'themes/default/'.$file.'.htm';
    }
    if (! file_exists ( $tplfile )) {
        exit ( $tplfile." is not exists!" );
    }
    
    $compiledtplfile = ROOTDIR.'cache/templates/'.$templateid.'_'.str_replace(array('/','\\'),'_',$file).'.tpl.php';
    if(!file_exists($compiledtplfile) || @filemtime($tplfile) > @filemtime($compiledtplfile)){
        loader::model('template')->template_compile($tplfile,$compiledtplfile);
    }
    return $compiledtplfile;
}

function arr_addslashes($arr){
    if(is_array($arr)){
        return array_map('arr_addslashes',$arr);
    }else{
        return addslashes($arr);
    }
}

function arr_stripslashes($arr){
    if(is_array($arr)){
        return array_map('arr_stripslashes',$arr);
    }else{
        return stripslashes($arr);
    }
}

function get_avatar($str){
    $avatar_prefix = 'http://www.gravatar.com/avatar.php?rating=G&size=48&gravatar_id=';
    $url = $avatar_prefix.md5($str);
    return $url;
}

function get_real_ip(){
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}
//check email avalible
function check_email($str){
    if(!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $str)){
        return false;
    }
    return true;
}

//Words Filter
function safe_convert($string, $html=0, $filterslash=0) {
    $string = stripslashes(trim($string));
    if ($html==0) {
        $string=htmlspecialchars($string, ENT_QUOTES);
        $string=str_replace("<","&lt;",$string);
        $string=str_replace(">","&gt;",$string);
        if ($filterslash==1) $string=str_replace("\\", '&#92;', $string);
    } else {
        $string=addslashes($string);
        if ($filterslash==1) $string=str_replace("\\\\", '&#92;', $string);
    }
    $string=str_replace("\r","",$string);
    $string=str_replace("\n","<br />",$string);
    $string=str_replace("\t","&nbsp;&nbsp;",$string);
    $string=str_replace("  ","&nbsp;&nbsp;",$string);
    $string=str_replace('|', '&#124;', $string);
    $string=str_replace("&amp;#96;","&#96;",$string);
    $string=str_replace("&amp;#92;","&#92;",$string);
    $string=str_replace("&amp;#91;","&#91;",$string);
    $string=str_replace("&amp;#93;","&#93;",$string);
    $string=preg_replace('/[a-zA-Z](&nbsp;)[a-zA-Z]/i',' ',$string);
    return $string;
}
//Transfer the converted words into editable characters
function safe_invert($string, $html=0) {
    $string = str_ireplace(array("<br />",'<br/>','<br>'),"\n",$string);
    if ($html!=0) {        
        $string = str_replace("<br/>","\n",$string);
        $string = str_replace("&nbsp;"," ",$string);
        //$string = str_replace("&","&amp;",$string);
    }
    $string = str_replace("&nbsp;"," ",$string);
    return $string;
}