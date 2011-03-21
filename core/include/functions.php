<?php

/*
 run page init
 initialize page head and user status
*/
function page_init($title = '',$keywords = '',$description='',$arr=array()){
    $auth =& loader::model('auth');
    $output =& loader::lib('output');
    
    $head_str = "<title>{$title} - Powered by Meiupic</title>\n";
    $head_str .= "<meta name=\"keywords\" content=\"{$keywords}\" />\n";
    $head_str .= "<meta name=\"description\" content=\"{$description}\" />\n";
    $output->set('meu_head',loader::lib('plugin')->filter('meu_head',$head_str,$arr));
    
    if(!$auth->loggedin()){
        $user_status = '<a href="#">登录</a>';
    }else{
        $user_status = '<span class="name">Lingter</span>
        <span class="pipe">|</span>
        <a title="查看和修改我的个人资料" href="#">我的资料</a>
        <span class="pipe">|</span>
        <a title="登出系统" href="#">登出</a>';
    }
    
    $output->set('user_status',loader::lib('plugin')->filter('user_status',$user_status));
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
    ob_start();
    require_once INCDIR.'template.func.php';
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