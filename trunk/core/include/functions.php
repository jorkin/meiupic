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

function get_sort_list($setting,$url,$sort){
    $str = '<div class="listorder f_right selectlist">
    <span class="label">排序:</span>';
    $token = rawurlencode('[#sort#]');
    $str .= '<div class="selected"></div><ul class="optlist">';
    foreach($setting as $k=>$v){
        if($v.'_asc' == $sort){
            $str .= '<li class="current"><a href="'.str_replace($token,$v.'_desc',$url).'" class="list_asc_on"><span>'.$k.'</span></a>';
        }elseif($v.'_desc' == $sort){
            $str .= '<li class="current"><a href="'.str_replace($token,$v.'_asc',$url).'" class="list_desc_on"><span>'.$k.'</span></a>';
        }else{
            $str .= '<li><a href="'.str_replace($token,$v.'_asc',$url).'" class="list_asc"><span>'.$k.'</span></a>';
        }
    }
    return $str.'</ul></div>';
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