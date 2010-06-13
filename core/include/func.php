<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */
function get_basepath(){
    if ($dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/')) {
      $base_path = "/$dir";
      $base_path .= '/';
    } else {
      $base_path = '/';
    }
    return $base_path;
}

function redirect($c){
    header("Location: $c");
    exit();
}

function redirect_c($ctl,$act='index'){
    header("Location: admin.php?ctl=$ctl&act=$act");
    exit();
}

function where_is_tmp(){
    $uploadtmp=ini_get('upload_tmp_dir');
    $envtmp=(getenv('TMP'))?getenv('TMP'):getenv('TEMP');
    if(is_dir($uploadtmp) && is_writable($uploadtmp))return $uploadtmp;
    if(is_dir($envtmp) && is_writable($envtmp))return $envtmp;
    if(is_dir('/tmp') && is_writable('/tmp'))return '/tmp';
    if(is_dir('/usr/tmp') && is_writable('/usr/tmp'))return '/usr/tmp';
    if(is_dir('/var/tmp') && is_writable('/var/tmp'))return '/var/tmp';
    return ".";
}

function imgSrc($img){
    global $setting;
    return $setting['imgdir'].'/'.$img;
}

function mkImgLink($dir,$key,$ext,$size='big'){
    global $setting;
    if($size=='orig'){
        return $setting['imgdir'].'/'.$dir.'/'.$key.'.'.$ext;
    }
    return $setting['imgdir'].'/'.$dir.'/'.$key.'_'.$size.'.'.$ext;
}

function get_updir_name($t){
    switch($t){
        case '1':
            $name = date('Y-m-d');
            break;
        case '2':
            $name =  date('Ymd');
            break;
        case '3':
            $name = date('Y-m');
            break;
        case '4':
            $name = date('Ym');
            break;
        case '5':
            $name = date('Y');
            break;
        default:
            $name = date('Y-m-d');
    }
    return $name;
}

function pageshow($total,$page,$url='',$pageset=5){
	
	$ppset = "";
	
	if($total>0){
		if($page<1 || $page=="")
		$page=1;
		if($page>$total)
		$page=$total;
		
		$ppset='<span class="pageset_total">共'.$total.'页</span> ';
		
		if($page>1)
		$ppset.='<a href="'.str_replace('[#page#]','1',$url).'">&lt;&lt;</a> <a href="'.str_replace('[#page#]',($page-1),$url).'" class="pre_page">&lt;</a> ';
		
		if(($page-$pageset)>1){
			$ppset.='<a href="'.str_replace('[#page#]','1',$url).'">1</a> ... ';
			for($i=$page-$pageset;$i<$page;$i++){
				$ppset.='<a href="'.str_replace('[#page#]',$i,$url).'">'.$i.'</a> ';
			}
		}
		else{
			for($i=1;$i<$page;$i++){
				$ppset.='<a href="'.str_replace('[#page#]',$i,$url).'">'.$i.'</a> ';
			}
		}
		
		$ppset.="<a href=\"".str_replace('[#page#]',$page,$url)."\" onclick=\"return false\" class=\"current\">$page</a> ";
		
		if(($page+$pageset)<$total){
			for($i=$page+1;$i<=($page+$pageset);$i++){
				$ppset.='<a href="'.str_replace('[#page#]',$i,$url).'">'.$i.'</a> ';
			}
			$ppset.=' ... <a href="'.str_replace('[#page#]',$total,$url).'">'.$total.'</a> ';
		}
		else{
			for($i=$page+1;$i<=$total;$i++){
				$ppset.='<a href="'.str_replace('[#page#]',$i,$url).'">'.$i.'</a> ';
			}
		}
		
		if($page<$total)
		$ppset.=' <a href="'.str_replace('[#page#]',($page+1),$url).'" class="next_page">&gt;</a> <a href="'.str_replace('[#page#]',$total,$url).'">&gt;&gt;</a>';
		
		return $ppset;
	}
	else{
		return  '<span class="pageset_total">共0页</span>';
	}
}

function showInfo($message,$flag = true,$link = '',$target = '_self'){
    $titlecolor = $flag?'infotitle2':'infotitle3';
    $otherlink = $link == '' ?"javascript:history.back();":$link;
    
    print <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="img/main.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="append_parent"></div>
<div class="container" id="cpcontainer"><h3>操作提示</h3><div class="infobox"><h4 class="$titlecolor">$message</h4><h5><a href="$otherlink" target="$target">返回</a></h5></div>
</div>
</body>
</html>
EOF;
    exit();
}

if(!function_exists('json_encode')){
    require_once (LIBDIR.'JSON.class.php');
    function json_encode($value){
        $json = new Services_JSON();
        return $json->encode($value);
    }
}
if(!function_exists('json_decode')){
    require_once (LIBDIR.'JSON.class.php');
    function json_decode($json_value,$bool = false){
        $json = new Services_JSON();
        return $json->decode($json_value,$bool);
    }
}