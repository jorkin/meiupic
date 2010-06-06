<?php
/**
 * $Id: db.class.php 13 2010-05-29 16:42:01Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class Image {
	/**
	 * 图片文件句柄
	 *
	 * @var image
	 */
	var $image;

	/**
	 * 图片类型
	 *
	 * @var imagetype
	 */
	var $image_type;
	
	var $image_quality=90;

	/**
	 * 装载图像
	 *
	 * @param string $filename 文件完整路径
	 * @return void
	 */
	function load($filename) {
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		if( $this->image_type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}else{
		    return false;
		}
	}
    
    function setQuality($q){
        if($q>0)
            $this->image_quality = $q;
    }
	/**
	 * 返回扩展名
	 * 
	 * @return string 扩展名
	 */
	function getExtension(){
		if( $this->image_type == IMAGETYPE_JPEG ) return 'jpg';
		elseif( $this->image_type == IMAGETYPE_GIF ) return 'gif';
		elseif( $this->image_type == IMAGETYPE_PNG ) return 'png';
	}

	/**
	 * 将图形对象保存成文件
	 *
	 * @param string $filename 文件名
	 * @param int $image_type 文件类型
	 * return volid
	 */
	function save($filename) {
	    $image_type = $this->image_type;
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$filename,$this->image_quality);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image,$filename,$this->image_quality);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image,$filename,$this->image_quality);
		}
	}
	
	/**
	 * 将图像输出到数据流
	 *
	 * @param int $image_type 文件类型
	 * @return void
	 */
	function output($image_type=IMAGETYPE_JPEG) {
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$this->image_quality);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image,$this->image_quality);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image,$this->image_quality);
		}
	}

	/**
	 * 获得图像宽度
	 *
	 * @return int 图像宽度
	 */
	function getWidth() {
		return imagesx($this->image);
	}

	/**
	 * 获得图像高度
	 *
	 * @return int 图像高度
	 */
	function getHeight() {
		return imagesy($this->image);
	}

	/**
	 * 等比例缩小到指定高度
	 * 
	 * @param int $height 指定高度
	 */
	function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}

	/**
	 * 缩小到指定尺寸
	 * 
	 * @param int $w 指定宽度
	 * @param int $h 指定高度
	 */
	function resizeTo($w=0, $h=0) {
		if($w>0 && $h>0) return $this->resize($w,$h);
		else if($w>0) return $this->resizeToWidth($w);
		else if($h>0) return $this->resizeToHeight($h);
	}
    /**
     * 指定最大宽度和最大高度
     * @param int $w 最大宽度
 	 * @param int $h 最大高度
     */
    function resizeScale($w=0,$h=0){
        if($w == 0 && $h>0){
            return $this->resizeToHeight($h);
        }
        if($h == 0 && $w>0){
            return $this->resizeToWidth($w);
        }
        if($w == 0 && $h==0){
            return false;
        }
        $maxwidth = $w;
        $maxheight = $h;
        
        $width = $this->getWidth();
        $height = $this->getHeight();
        
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
                return $this->resizeToWidth($w);
            }else{
                return $this->resizeToHeight($h);
            }
        }elseif($RESIZEWIDTH){
            return $this->resizeToWidth($w);
        }elseif($RESIZEHEIGHT){
            return $this->resizeToHeight($h);
        }
    }
    /**
	 * 等比例缩小到指定宽度，并切成方形
	 * 
	 * @param int $v 指定宽度/高度
	 */
    function square($v){
        $width = $this->getWidth();
        $height = $this->getHeight();
        $left = 0;
        $right = 0;
        if($width>$height){
            $this->resizeToHeight($v);
            $left = ceil(($v/$height * $width - $v)/2); 
        }else{
            $this->resizeToWidth($v);
            $top = ceil(($v/$width * $height - $v)/2); 
        }
        $this->cut($v,$v,$left,$top);
    }
	/**
	 * 等比例缩小到指定宽度
	 * 
	 * @param int $width 指定宽度
	 */
	function resizeToWidth($width) {
		if($width>=$this->getWidth()) return;
		$ratio = $width / $this->getWidth();
		$height = $this->getHeight() * $ratio;
		$this->resize($width,$height);
	}

	/**
	 * 维持宽高比缩小指定比例
	 * 
	 * @param int $scale 指定比例
	 */
	function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getHeight() * $scale/100;
		$this->resize($width,$height);
	}

	/**
	 * 改变图像尺寸
	 * 
	 * @param int $width 指定宽度
	 * @param int $height 指定高度
	 */
	function resize($width,$height) {
	    if(function_exists("imagecopyresampled")){
            $newim = imagecreatetruecolor($width, $height);
            imagecopyresampled($newim, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        }else{
            $newim = imagecreate($width, $height);
            imagecopyresized($newim, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        }
        $this->image = $newim;
	}

	/**
	 * 裁剪图像
	 *
	 * @param int $width 指定宽度
	 * @param int $height 指定高度
	 */
	function cut($width,$height,$left = 0,$top = 0){
		$new_image = imagecreatetruecolor($width, $height);
		imagecopy($new_image, $this->image, 0, 0, $left, $top, $width, $height);
		$this->image = $new_image;
	}

	/**
	 * 截取从某纵向位置开始指定高度的图像
	 *
	 * @param int $top 指定位置
	 * @param int $height 指定高度
	 */
	function vcut($top,$height){
		$width = $this->getWidth();
		$height = $this->getHeight()-$top+$height;
		if($height<200) return;
		$new_image = imagecreatetruecolor($width, $height);
		imagecopy($new_image, $this->image, 0, 0, 0, $top, $width, $height);
		$this->image = $new_image;
	}

	/**
	 * 获取图片EXIF信息
	 */
	function GetImageInfo($img) {
        if(!function_exists('exif_read_data')){
            return false;
        }
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
            "相机品牌" => $exif[IFD0][Make],
            "相机型号" => $exif[IFD0][Model],
            "曝光模式" => ($exif[EXIF][ExposureMode]==1?"手动":"自动"),
            "闪光灯" =>  isset($Flash_arr[$exif[EXIF][Flash]])?$Flash_arr[$exif[EXIF][Flash]]:'未知',
            "焦距" => $exif[EXIF][FocalLength]."mm",
            "光圈" => $exif[COMPUTED][ApertureFNumber],
            "快门速度" => $exif[EXIF][ExposureTime],
            "ISO感光度" => $exif[EXIF][ISOSpeedRatings],
            "白平衡" => ($exif[EXIF][WhiteBalance]==1?"手动":"自动"),
            "曝光补偿" => $exif[EXIF][ExposureBiasValue]."EV",
            "拍摄时间" => $exif[EXIF][DateTimeOriginal]
        );
        }
        return $new_img_info;
    }
}