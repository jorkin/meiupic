<?php
/**
 * $Id$
 * Global functions
 * @author : Lingter
 * @support : http://meiupic.meiu.cn
 * @copyright : (c)2011 meiu.cn lingter@gmail.com
 */
if(!function_exists('file_put_contents')) {
    function file_put_contents($filename, $s) {
        $fp = @fopen($filename, 'w');
        @fwrite($fp, $s);
        @fclose($fp);
        return TRUE;
    }
}
function get_remote($url,$timeout = 15, $limit = 0, $post = '', $cookie = '', $ip = '',  $block = TRUE){
    if(function_exists('fsockopen') || function_exists('pfsockopen')){
        return socket_get_content($url, $timeout , $limit , $post , $cookie , $ip,  $block);
    }else{
        $ctx = null;
        if($timeout>0){
            if(function_exists('stream_context_create')){
                if($post){
                    $data = http_build_query($post, '', '&');
                    $par = array(
                        'http' => array(
                            'method'=>'POST',
                            'timeout'=>$timeout,
                            'header'=>"Content-Type: application/x-www-form-urlencoded\r\n".
                            "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n".
                            "Content-Length: " . strlen($data) . "\r\n".
                            "Cookie: $cookie\r\n",
                            'content' => $data,
                        )
                    );
                }else{
                    $par = array(
                        'http' => array(
                            'method'=>'GET',
                            'timeout'=>$timeout,
                            'header'=>"Content-Type: application/x-www-form-urlencoded\r\n".
                            "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n".
                            "Cookie: $cookie\r\n",
                        )
                    );
                }
                $ctx = stream_context_create($par);
            }
        }
        $result = @file_get_contents($url,false,$ctx);
        return $result;
    }
}

function socket_get_content($url, $timeout = 15, $limit = 0, $post = '', $cookie = '', $ip = '',  $block = TRUE) {
    $return = '';
    $matches = parse_url($url);
    $host = $matches['host'];
    $path = $matches['path'] ? $matches['path'].(isset($matches['query']) && $matches['query'] ? '?'.$matches['query'] : '') : '/';
    $port = !empty($matches['port']) ? $matches['port'] : 80;

    if($post) {
        $out = "POST $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= 'Content-Length: '.strlen($post)."\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cache-Control: no-cache\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
        $out .= $post;
    } else {
        $out = "GET $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
    }

    if(function_exists('fsockopen')) {
        $fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    } elseif (function_exists('pfsockopen')) {
        $fp = @pfsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    } else {
        $fp = false;
    }

    if(!$fp) {
        return '';
    } else {
        stream_set_blocking($fp, $block);
        stream_set_timeout($fp, $timeout);
        @fwrite($fp, $out);
        $status = stream_get_meta_data($fp);
        if(!$status['timed_out']) {
            while (!feof($fp)) {
                if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                    break;
                }
            }

            $stop = false;
            while(!feof($fp) && !$stop) {
                $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                $return .= $data;
                if($limit) {
                    $limit -= strlen($data);
                    $stop = $limit <= 0;
                }
            }
        }
        @fclose($fp);
        return $return;
    }
}
//create link
function site_link($ctl='default',$act='index',$pars=array()){
    $uri =& loader::lib('uri');
    return $uri->mk_uri($ctl,$act,$pars);
}

function redirect($url,$time=0,$msg=''){
    $url = str_replace(array("\n", "\r"), '', $url);
    if(empty($msg))
        $msg  =  "This page will redirect to {$url} in {$time} seconds！";
    
    if (!headers_sent()) {
        if(0===$time) {
            header("Location: ".$url);
        }else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    }else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time!=0)
            $str   .=   $msg;
        exit($str);
    }
}

/*
type:
    page: 页面
    ajax_page: ajax 页面
    ajax: 返回ajax
*/
function need_login($type = 'page'){
    if(!loader::model('user')->loggedin()){
        switch($type){
            case 'page':
                showError(lang('not_authorized'));
                break;
            case 'ajax_page':
                ajax_box(lang('not_authorized'));
                break;
            case 'ajax_inline':
                echo '<form><div class="form_notice_div">'.lang('not_authorized').'</div><input type="button" name="cancel" class="graysmlbtn" value="'.lang('cancel').'" /></form>';
                break;
            case 'ajax':
                form_ajax_failed('text',lang('not_authorized'));
                break;
            case 'ajax_box':
                form_ajax_failed('box',lang('not_authorized'));
                break;
            case 'ajax_bubble':
                echo '<div class="close png" onclick="Mui.bubble.close()">X</div>'.lang('not_authorized');
                break;
        }
        exit;
    }
}

function no_cache_header(){
    header("Content-Type:text/xml;charset=utf-8");
    header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
}

function form_ajax_success($type = 'box|text', $content , $title = null, $close_time = 0 , $forward = ''){
    form_ajax($type,true,$content,$title,$close_time,$forward);
}

function form_ajax_failed($type = 'box|text', $content , $title = null, $close_time = 0 , $forward = ''){
    form_ajax($type,false,$content,$title,$close_time,$forward);
}

function form_ajax($type = 'box|text', $flag , $content , $title = null, $close_time = 0 , $forward = ''){
    no_cache_header();
    
    if($type == 'box'){
        $content = ajax_box($content,$title,$close_time,$forward,false);
    }
    echo loader::lib('json')->encode(
        array('ret'=>$flag,'html'=>$content)
    );
    exit;
}

function ajax_box( $content , $title = '', $close_time = 0 , $forward = '' , $display = true )
{   
    if(!$title){
        $title = lang('system_notice');
    }
    $_config = loader::model('setting')->get_conf('theme_'.TEMPLATEID,array());
    ob_start();
    include template('block/ajax_box');
    $page_content = ob_get_clean();
    if($display){
        //no_cache_header();
        echo $page_content;
        exit;
    }else{
        return $page_content;
    }
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
    <span class="label">'.lang('sort').':</span>';
    $str .= '<div class="selected"></div><ul class="optlist">';
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
    $str = $str.'</ul></div>';
    return array($sort,$str);
}

function get_page_setting($type){
    $arr = array(12,30,56);
    $current = isset($_COOKIE['Mpic_pageset_'.$type])?$_COOKIE['Mpic_pageset_'.$type]:'12';
    
    $str = '<div class="pset f_right selectlist">
        <span class="label">'.lang('show_nums_per_page').':</span>';
    $str .= '<div class="selected"></div><ul class="optlist">';
    foreach($arr as $v){
        $str .= '<li '.($current==$v?'class="current"':'').'><a href="javascript:void(0);" onclick="page_setting(\''.$type.'\','.$v.');">'.$v.'</a></li>';
    }
    $str .= '</ul></div>';
    return array($current,$str);
}

function template($file,$templateid=null,$tpldir=null) {
    if(strpos($file,':')!==false ) {
        list($templateid, $file) = explode(':', $file);
        $tpldir = 'plugins/'.$templateid.'/templates';
    }
    $tpldir = $tpldir?$tpldir:TPLDIR;
    $templateid = $templateid ? $templateid : TEMPLATEID;
    
    $tplfile = ROOTDIR.$tpldir.'/'.$file.'.htm';
    
    if(TEMPLATEID != 1 && !file_exists($tplfile)) {
        $tplfile = ROOTDIR.'themes/default/'.$file.'.htm';
    }
    if (! file_exists ( $tplfile )) {
        exit(lang('file_not_exists',$tplfile));
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

function get_album_cover($aid,$ext){
    return 'data/cover/'.$aid.'.'.$ext;
}

function get_avatar($comment){
    $gravatar_url = GRAVATAR_URL;
    $id = md5(strtolower($comment['email']));
    $url = str_replace('{idstring}',$id,$gravatar_url);
    
    return $url;
}

function img_path($path){
    $plugin =& loader::lib('plugin');
    return $plugin->filter('photo_path',$GLOBALS['base_path'].$path,$path);
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

//Words Filter
function safe_convert($string, $html=false, $filterslash=false) {
    $string = stripslashes(trim($string));
    if (!$html) {
        $string=htmlspecialchars($string, ENT_QUOTES);
        $string=str_replace("<","&lt;",$string);
        $string=str_replace(">","&gt;",$string);
        if ($filterslash) $string=str_replace("\\", '&#92;', $string);
        
        $string=str_replace('|', '&#124;', $string);
        $string=str_replace("&amp;#96;","&#96;",$string);
        $string=str_replace("&amp;#92;","&#92;",$string);
        $string=str_replace("&amp;#91;","&#91;",$string);
        $string=str_replace("&amp;#93;","&#93;",$string);
    } else {
        //$string=addslashes($string);
        if ($filterslash) $string=str_replace("\\\\", '&#92;', $string);
    }
    $string=str_replace("\r","",$string);
    $string=str_replace("\n","<br />",$string);
    $string=str_replace("\t","&nbsp;&nbsp;",$string);
    $string=str_replace("  ","&nbsp;&nbsp;",$string);
    $string=preg_replace('/[a-zA-Z](&nbsp;)[a-zA-Z]/i',' ',$string);
    return $string;
}
//Transfer the converted words into editable characters
function safe_invert($string, $html=false) {
    $string = str_ireplace(array("<br />",'<br/>','<br>'),"\n",$string);
    if ($html) {        
        $string = str_replace("<br/>","\n",$string);
        $string = str_replace("&nbsp;"," ",$string);
        //$string = str_replace("&","&amp;",$string);
    }
    $string = str_replace("&nbsp;"," ",$string);
    return $string;
}



function touch_file($file,$content = ''){
    $flag = @file_put_contents($file,$content);
    return $flag;
}

/*
flag: 1, new files added
      2, clear trash
*/
function trash_status($flag){
    if($flag == 1){
        touch_file(ROOTDIR.'cache/data/trash.tmp');
    }else{
        @unlink(ROOTDIR.'cache/data/trash.tmp');
    }
}

function has_trash(){
    if(file_exists(ROOTDIR.'cache/data/trash.tmp')){
        return true;
    }
    return false;
}

function showError($error_msg){
    loader::lib('output')->set('error_msg',$error_msg);
    loader::view('block/showerror');
    exit;
}

function file_ext($filename){
    return strtolower(end(explode('.',$filename)));
}
function file_pure_name($filename){
    $arr = explode('.',$filename);
    array_pop($arr);
    return implode('.',$arr);
}

//covert bytes to be readable
function bytes2u($size){
    $result = '';
    if($size < 1024)
    {
      $result = round($size, 2).' B';
    }
    elseif($size < 1024*1024)
    {
      $result = round($size/1024, 2).' KB';
    }
    elseif($size < 1024*1024*1024)
    {
      $result = round($size/1024/1024, 2).' MB';
    }
    elseif($size < 1024*1024*1024*1024)
    {
      $result = round($size/1024/1024/1024, 2).' GB';
    }
    else
    {
      $result = round($size/1024/1024/1024/1024, 2).' TB';
    }
    return $result;
}

function allowsize($size){
    switch($size){
        case '512K':
            $byte = 512*1024;
            break;
        case '1M':
            $byte = 1024*1024;
            break;
        case '2M':
            $byte = 2*1024*1024;
            break;
        case '5M':
            $byte = 5*1024*1024;
            break;
        default:
            $byte = false;
    }
    return $byte;
}


function get_fonts(){
    $fontdir = ROOTDIR.'statics/font';
    $fonts = array();
    if($directory = @dir($fontdir)) {
        while($entry = $directory->read()) {
            $fileext = file_ext($entry);
            if(strtolower($fileext) == 'ttf' || strtolower($fileext) == 'ttc'){
                $fonts[] = $entry;
            }
        }
        $directory->close();
    }
    return $fonts;
}

/* validate start */
//check email avalible
function check_email($str){
    if(!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $str)){
        return false;
    }
    return true;
}
function check_color($c){
    if(preg_match('/^\#([0-9A-F]{2}[0-9A-F]{2}[0-9A-F]{2}|[0-9A-F]{3})$/i', $c)){
        return true;
    }
    return false;
}
/* validate end */

function deldir($dir) {
    if($directory = @dir($dir)) {
        while ($file = $directory->read()) {
            if($file!="." && $file!="..") {
              $fullpath=$dir."/".$file;
              if(!is_dir($fullpath)) {
                  @unlink($fullpath);
              } else {
                  deldir($fullpath);
              }
            }
        }
        $directory->close();
    }else{
        return false;
    }
    if(@rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}

function dirsize($dir) {
    @$dh = opendir($dir);
    $size = 0;
    while ($file = @readdir($dh)) {
        if ($file != "." and $file != "..") {
            $path = $dir."/".$file;
            if (is_dir($path)) {
                $size += dirsize($path);
            } elseif (is_file($path)) {
                $size += filesize($path);
            }
        }
    }
    @closedir($dh);
    return $size;
}

function dir_clear($dir) {
    if($directory = @dir($dir)) {
        while($entry = $directory->read()) {
            $filename = $dir.'/'.$entry;
            if(is_file($filename)) {
                @unlink($filename);
            }
        }
        $directory->close();
        @touch($dir.'/index.htm');
    }
}