<?php

define('FCPATH',__FILE__);
define('ROOTDIR',dirname(FCPATH).DIRECTORY_SEPARATOR);

define('COREDIR',ROOTDIR.'core'.DIRECTORY_SEPARATOR);
define('LIBDIR',COREDIR.'libs'.DIRECTORY_SEPARATOR);
define('INCDIR',COREDIR.'include'.DIRECTORY_SEPARATOR);
define('CTLDIR',COREDIR.'ctls'.DIRECTORY_SEPARATOR);
define('VIEWDIR',COREDIR.'views'.DIRECTORY_SEPARATOR);
define('MODELDIR',COREDIR.'models'.DIRECTORY_SEPARATOR);
define('DATADIR',ROOTDIR.'data'.DIRECTORY_SEPARATOR);
define('PLUGINDIR',ROOTDIR.'plugins'.DIRECTORY_SEPARATOR);
define('MAGIC_GPC',get_magic_quotes_gpc());

if (floor(PHP_VERSION) < 5){
    define('PHPVer',4);
}else{
    define('PHPVer',5);
}
require_once(COREDIR.'loader.php');
require_once(INCDIR.'functions.php');

class thumb{
    var $file_pre_block = "<?php die('Execution denied!'); //";
    var $cache_file = '';
    var $cache_dir = '';
    var $realpath = '';
    var $param = array();
    var $imgHandler = null;
    var $cleanCacheTime = 43200; //每隔多长时间检查清理缓存 默认半天
    var $fileCacheAge = 86400; //文件缓存有效期 默认一天
    
    function thumb(){
        date_default_timezone_set('UTC');

        $path = getGet('path');
        $this->realpath = get_realpath(ROOTDIR.$path);
        $this->cache_dir = ROOTDIR.'cache/dimgs/';

        $this->param['w'] = intval(getGet('w',0));
        $this->param['h'] = intval(getGet('h',0));
        $this->param['square'] = intval(getGet('square',0));
        $this->param['zoom'] = getGet('zoom',0);
        
        $open_cache = intval(getGet('cache',0));

        $cache_key = md5($path.$this->param['w'].$this->param['h'].$this->param['square'].$this->param['zoom'] );
        $this->cache_subdir = $this->cache_dir.substr($cache_key,0,2);

        $this->cache_file = $this->cache_subdir.'/'.$cache_key.'.php';

        if($this->browserCache()){ //尝试浏览器缓存
            exit();
        }
        
        if($open_cache){//如果开启了服务器缓存
            $this->cleanCache();

            if($this->serverCache()){ //尝试服务器缓存
                exit();
            }

            if(!$this->processImage()){//处理图片
                exit('Process image failed!');
            }
            
            if($this->writeToCache()){//写入缓存
                $this->serverCache();
            }
        }else{
            if(!$this->processImage()){
                exit('Process image failed!');
            }
            $this->outputImg();//直接输出图片
        }
    }
    
    //路径检查，如果不是网站目录下的图片，禁止访问
    //如果原始文件不存在报404错误
    function pathCheck(){
        if(stripos($this->realpath, ROOTDIR) !== 0){
            //非法的路径
            exit('Access denied!');
        }

        if(!file_exists($this->realpath)){//原始文件不存在
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found!');
            exit;
        }
    }
    
    //浏览器缓存
    function browserCache(){
        if(!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) ){
            $mtime = filemtime($this->realpath);
            $iftime = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            
            if($iftime < 1){
                return false;
            }
            if($iftime < $mtime){
                return false;
            } else {
                header ($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
                return true;
            }
        }
        return false;
    }
    
    //服务端缓存
    function serverCache(){
        if(!is_dir($this->cache_dir)){
            @mkdir($this->cache_dir);
        }

        if(! is_file($this->cache_file)){
            return false;
        }
        $fp = fopen($this->cache_file, 'rb');
        if(! $fp){ exit("Could not open cachefile."); }

        fseek($fp, strlen($this->file_pre_block), SEEK_SET);
        $imgType = fread($fp, 3);
        fseek($fp, 3, SEEK_CUR);
        if(ftell($fp) != strlen($this->file_pre_block) + 6){
            @unlink($this->cachefile);
            exit("The cached image file seems to be corrupt.");
        }

        fclose($fp);
        $content = file_get_contents($this->cache_file);
        if ($content != FALSE) {
            $content = substr($content, strlen($this->file_pre_block) + 6);
            $mimeType = 'image/'.$imgType;
            if($mimeType == 'image/jpg'){
                $mimeType = 'image/jpeg';
            }
            $this->imageHeader($mimeType);
            echo $content;
            return true;
        }
        return false;
    }
    
    //输出头，包括缓存的header
    function imageHeader($mimeType = ''){
        $gmdate_expires = gmdate ('D, d M Y H:i:s', strtotime ('now +10 days')) . ' GMT';
        $gmdate_modified = gmdate ('D, d M Y H:i:s') . ' GMT';
        $interval = 60*60*12; //浏览器缓存12小时失效

        header ('Last-Modified: ' .$gmdate_modified);
        header ('Accept-Ranges: none');
        header ("Expires: " . $gmdate_expires);
        header ("Cache-Control: max-age=$interval, must-revalidate");
        if($mimeType){
            header('Content-Type: '.$mimeType);
        }
    }
    
    //处理图片
    function processImage(){
        $this->imgHandler =& loader::lib('image');
        if(!$this->imgHandler->load($this->realpath)){
            return false;
        }

        if($this->param['zoom'] ){
            $this->imgHandler->resizeScale($this->param['w'],$this->param['h']);
        }elseif($this->param['square']){
            $this->imgHandler->square($this->param['w']);
        }elseif($this->param['w'] || $this->param['h']){
            $this->imgHandler->resizeTo($this->param['w'],$this->param['h']);
        }else{
            return false;
        }
        return true;
    }
    
    //将图片直接输出
    function outputImg(){
        $this->imageHeader();
        $this->imgHandler->output();
    }
    
    //写入到缓存
    function writeToCache(){
        if(!is_dir($this->cache_subdir)){
            @mkdir($this->cache_subdir);
        }

        $tempfile = tempnam($this->cache_dir, 'tmpimg_');
        $this->imgHandler->save($tempfile);
        
        $imgType = $this->imgHandler->getExtension();
        
        $tempfile4 = tempnam($this->cache_dir, 'tmpimg_');
        $context = stream_context_create();
        $fp = fopen($tempfile,'r',0,$context);
        file_put_contents($tempfile4, $this->file_pre_block . $imgType . ' ?' . '>'); //6 extra bytes, first 3 being image type 
        file_put_contents($tempfile4, $fp, FILE_APPEND);
        fclose($fp);
        @unlink($tempfile);
        $lockFile = $this->cache_file . '.lock';
        $fh = fopen($lockFile, 'w');
        if(! $fh){
            return false;//不能打开lock文件
        }
        if(flock($fh, LOCK_EX)){
            @unlink($this->cache_file);
            rename($tempfile4, $this->cache_file);
            flock($fh, LOCK_UN);
            fclose($fh);
            @unlink($lockFile);
        } else {
            fclose($fh);
            @unlink($lockFile);
            @unlink($tempfile4);
        }
        return true;
    }
    //清除缓存
    function cleanCache(){
		if ($this->cleanCacheTime < 0) {
			return;
		}
		$lastCleanFile = $this->cache_dir . '/cacheLastCleanTime.touch';
		
		if(! is_file($lastCleanFile)){
			touch($lastCleanFile);
			return;
		}
		if(@filemtime($lastCleanFile) < (time() - $this->cleanCacheTime) ){
			touch($lastCleanFile);

			$files = glob($this->cache_dir . '/[a-zA-Z0-9][a-zA-Z0-9]/*.php');//用glob通过通配符搜索所有缓存文件
			if ($files) {
				$timeAgo = time() - $this->fileCacheAge;
				foreach($files as $file){
					if(@filemtime($file) < $timeAgo){
						@unlink($file);
					}
				}
			}
			return true;
		}
		return false;
	}
}

//开始执行
new thumb();