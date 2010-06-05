<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

function redirect($c){
    header("Location: $c");
    exit();
}

function redirect_c($ctl,$act='index'){
    header("Location: index.php?ctl=$ctl&act=$act");
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

function ResizeImage($img,$maxwidth,$maxheight,$name=NULL){
    $info = getimagesize($img);
    if($info[2] == 2){
        $im = imagecreatefromjpeg($img);
    }elseif($info[2] == 3){
        $im = imagecreatefrompng($img);
    }elseif($info[2] == 1){
        $im = imagecreatefromgif($img);
    }
    if(!$name){
        header("Content-type: image/jpeg");
    }
    $width = imagesx($im);
    $height = imagesy($im);
    if(($maxwidth && $width > $maxwidth) || ($maxheight && $height > $maxheight)){
        if($maxwidth && $width > $maxwidth){
            $widthratio = $maxwidth/$width;
            $RESIZEWIDTH=true;
        }
        if($maxheight && $height > $maxheight){
            $heightratio = $maxheight/$height;
            $RESIZEHEIGHT=true;
        }
        if($RESIZEWIDTH && $RESIZEHEIGHT){
            if($widthratio < $heightratio){
                $ratio = $widthratio;
            }else{
                $ratio = $heightratio;
            }
        }elseif($RESIZEWIDTH){
            $ratio = $widthratio;
        }elseif($RESIZEHEIGHT){
            $ratio = $heightratio;
        }
        $newwidth = $width * $ratio;
        $newheight = $height * $ratio;
        if(function_exists("imagecopyresampled")){
            $newim = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        }else{
            $newim = imagecreate($newwidth, $newheight);
            imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        }
        ImageJpeg ($newim,$name,100);
        ImageDestroy ($newim);
    }else{
        ImageJpeg ($im,$name);
        ImageDestroy ($im,$name);
    }
}

function imgSrc($img){
    global $setting;
    return $setting['imgdir'].'/'.$img;
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

function GetImageInfoVal($ImageInfo,$val_arr) {
    $InfoVal = "未知";
    foreach($val_arr as $name=>$val) {
    if ($name==$ImageInfo) {
    $InfoVal = &$val;
    break;
    }
    }
    return $InfoVal;
}

function GetImageInfo($img) {
    if(!function_exists('exif_read_data')){
        return false;
    }
    $imgtype = array("", "GIF", "JPG", "PNG", "SWF", "PSD", "BMP", "TIFF(intel byte order)", "TIFF(motorola byte order)", "JPC", "JP2", "JPX", "JB2", "SWC", "IFF", "WBMP", "XBM");
    $Orientation = array("", "top left side", "top right side", "bottom right side", "bottom left side", "left side top", "right side top", "right side bottom", "left side bottom");
    $ResolutionUnit = array("", "", "英寸", "厘米");
    $YCbCrPositioning = array("", "the center of pixel array", "the datum point");
    $ExposureProgram = array("未定义", "手动", "标准程序", "光圈先决", "快门先决", "景深先决", "运动模式", "肖像模式", "风景模式");
    $MeteringMode_arr = array(
        "0" => "未知",
        "1" => "平均",
        "2" => "中央重点平均测光",
        "3" => "点测",
        "4" => "分区",
        "5" => "评估",
        "6" => "局部",
        "255" => "其他"
    );
    $Lightsource_arr = array(
        "0" => "未知",
        "1" => "日光",
        "2" => "荧光灯",
        "3" => "钨丝灯",
        "10" => "闪光灯",
        "17" => "标准灯光A",
        "18" => "标准灯光B",
        "19" => "标准灯光C",
        "20" => "D55",
        "21" => "D65",
        "22" => "D75",
        "255" => "其他"
    );
    $Flash_arr = array(
        0x00 => "关闭",
        0x01 => "开启",
        0x05 => "打开(不探测返回光线)",
        0x07 => "打开(探测返回光线)",
        0x09 => "打开(强制)",
        0x0D => "打开(强制/不探测返回光线)",
        0x0F => "打开(强制/探测返回光线)",
        0x10 => "关闭(强制)",
        0x18 => "关闭(自动)",
        0x19 => "打开(自动)",
        0x1D => "打开(自动/不探测返回光线)",
        0x1F => "打开(自动/探测返回光线)",
        0x20 => "没有闪光功能",
        0x41 => "打开(防红眼)",
        0x45 => "打开(防红眼/不探测返回光线)",
        0x47 => "打开(防红眼/探测返回光线)",
        0x49 => "打开(强制/防红眼)",
        0x4D => "打开(强制/防红眼/不探测返回光线)",
        0x4F => "打开(强制/防红眼/探测返回光线)",
        0x59 => "打开(自动/防红眼)",
        0x5D => "打开(自动/防红眼/不探测返回光线)",
        0x5F => "打开(自动/防红眼/探测返回光线)"
    );
    
    $exif = @exif_read_data($img,"IFD0");
    if ($exif===false) {
        return false;
    }
    else
    {
    $exif = exif_read_data ($img,0,true);
    $new_img_info = array (
        "文件名" => $exif[FILE][FileName],
        "文件类型" => $imgtype[$exif[FILE][FileType]],
        "文件格式" => $exif[FILE][MimeType],
        "文件大小" => $exif[FILE][FileSize],
        "时间戳" => date("Y-m-d H:i:s",$exif[FILE][FileDateTime]),
        "图片说明" => $exif[IFD0][ImageDescription],
        "制造商" => $exif[IFD0][Make],
        "型号" => $exif[IFD0][Model],
        "方向" => $Orientation[$exif[IFD0][Orientation]],
        "水平分辨率" => $exif[IFD0][XResolution].$ResolutionUnit[$exif[IFD0][ResolutionUnit]],
        "垂直分辨率" => $exif[IFD0][YResolution].$ResolutionUnit[$exif[IFD0][ResolutionUnit]],
        "创建软件" => $exif[IFD0][Software],
        "修改时间" => $exif[IFD0][DateTime],
        "作者" => $exif[IFD0][Artist],
        "YCbCr位置控制" => $YCbCrPositioning[$exif[IFD0][YCbCrPositioning]],
        "版权" => $exif[IFD0][Copyright],
        "摄影版权" => $exif[COMPUTED][Copyright.Photographer],
        "编辑版权" => $exif[COMPUTED][Copyright.Editor],
        "Exif版本" => $exif[EXIF][ExifVersion],
        "FlashPix版本" => "Ver. ".number_format($exif[EXIF][FlashPixVersion]/100,2),
        "拍摄时间" => $exif[EXIF][DateTimeOriginal],
        "数字化时间" => $exif[EXIF][DateTimeDigitized],
        "拍摄分辨率高" => $exif[COMPUTED][Height],
        "拍摄分辨率宽" => $exif[COMPUTED][Width],

        "光圈" => $exif[EXIF][ApertureValue],
        "快门速度" => $exif[EXIF][ShutterSpeedValue],
        "快门光圈" => $exif[COMPUTED][ApertureFNumber],
        "最大光圈值" => "F".$exif[EXIF][MaxApertureValue],
        "曝光时间" => $exif[EXIF][ExposureTime],
        "F-Number" => $exif[EXIF][FNumber],
        "测光模式" => GetImageInfoVal($exif[EXIF][MeteringMode],$MeteringMode_arr),
        "光源" => GetImageInfoVal($exif[EXIF][LightSource], $Lightsource_arr),
        "闪光灯" => GetImageInfoVal($exif[EXIF][Flash], $Flash_arr),
        "曝光模式" => ($exif[EXIF][ExposureMode]==1?"手动":"自动"),
        "白平衡" => ($exif[EXIF][WhiteBalance]==1?"手动":"自动"),
        "曝光程序" => $ExposureProgram[$exif[EXIF][ExposureProgram]],
    
        "曝光补偿" => $exif[EXIF][ExposureBiasValue]."EV",
        "ISO感光度" => $exif[EXIF][ISOSpeedRatings],
        "分量配置" => (bin2hex($exif[EXIF][ComponentsConfiguration])=="01020300"?"YCbCr":"RGB"),
        "图像压缩率" => $exif[EXIF][CompressedBitsPerPixel]."Bits/Pixel",
        "对焦距离" => $exif[COMPUTED][FocusDistance]."m",
        "焦距" => $exif[EXIF][FocalLength]."mm",
        "等价35mm焦距" => $exif[EXIF][FocalLengthIn35mmFilm]."mm",
 
        "用户注释编码" => $exif[COMPUTED][UserCommentEncoding],
        "用户注释" => $exif[COMPUTED][UserComment],
        "色彩空间" => ($exif[EXIF][ColorSpace]==1?"sRGB":"未校准"),
        "Exif图像宽度" => $exif[EXIF][ExifImageLength],
        "Exif图像高度" => $exif[EXIF][ExifImageWidth],
        "缩略图文件格式" => $exif[COMPUTED][Thumbnail.FileType],
        "缩略图Mime格式" => $exif[COMPUTED][Thumbnail.MimeType]
    );
    }
    return $new_img_info;
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