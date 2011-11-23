<?php
//表单提交成功提示
function form_ajax_success($type = 'box|text', $content , $title = null, $close_time = 0 , $forward = ''){
    form_ajax($type,true,$content,$title,$close_time,$forward);
}
//表单提交失败提示
function form_ajax_failed($type = 'box|text', $content , $title = null, $close_time = 0 , $forward = ''){
    form_ajax($type,false,$content,$title,$close_time,$forward);
}

function form_ajax($type = 'box|text', $flag , $content , $title = null, $close_time = 0 , $forward = ''){
    no_cache_header();
    
    if($type == 'box'){
        $content = ajax_box($content,$title,$close_time,$forward,false);
    }
    $json =& loader::lib('json');
    echo $json->encode(
        array('ret'=>$flag,'html'=>$content)
    );
    exit;
}

//浮动图层内容
function ajax_box( $content , $title = '', $close_time = 0 , $forward = '' , $display = true )
{   
    if(!$title){
        $title = lang('system_notice');
    }
    $output=&loader::lib('output');
    $output->set('content',$content);
    $output->set('title',$title);
    $output->set('close_time',$close_time);
    $output->set('forward',$forward);
    if($display){
        loader::view('block/ajax_box');
        exit;
    }else{
        return loader::view('block/ajax_box',false);
    }
}

//排序下拉菜单
function get_sort_list($setting,$type,$default){
    $str = '<div class="dropmenu f_right listorder">
            <span class="label">'.lang('sort').':</span>';
    $str .= '<div class="selectlist"><div class="selected"></div><ul class="optlist">';
    $sort = isset($_COOKIE['Mpic_sortset_'.$type])?$_COOKIE['Mpic_sortset_'.$type]:$default;
    foreach($setting as $k=>$v){
        if($v.'_asc' == $sort){
            $str .= '<li class="current"><a href="javascript:void(0);" onclick="sort_setting(\''.$type.'\',\''.$v.'_desc\');" class="list_asc_on"><span>'.$k.'</span></a>';
        }elseif($v.'_desc' == $sort){
            $str .= '<li class="current"><a href="javascript:void(0);" onclick="sort_setting(\''.$type.'\',\''.$v.'_asc\');" class="list_desc_on"><span>'.$k.'</span></a>';
        }else{
            $str .= '<li><a href="javascript:void(0);" onclick="sort_setting(\''.$type.'\',\''.$v.'_asc\');" class="list_asc"><span>'.$k.'</span></a>';
        }
    }
    $str = $str.'</ul></div></div>';
    return array($sort,$str);
}
//排序分页菜单
function get_page_setting($type){
    $arr = array(12,30,56);
    $current = isset($_COOKIE['Mpic_pageset_'.$type])?$_COOKIE['Mpic_pageset_'.$type]:'12';
    
    $str = '<div class="dropmenu pset f_right">
        <span class="label">'.lang('show_nums_per_page').':</span>';
    $str .= '<div class="selectlist"><div class="selected"></div><ul class="optlist">';
    foreach($arr as $v){
        $str .= '<li '.($current==$v?'class="current"':'').'><a href="javascript:void(0);" onclick="page_setting(\''.$type.'\','.$v.');"><span>'.$v.'</span></a></li>';
    }
    $str .= '</ul></div></div>';
    return array($current,$str);
}