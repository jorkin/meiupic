<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 - 2011 meiu.cn lingter@gmail.com
 */

class storage_mdl extends modelfactory{
    //Temp file age in seconds
    var $maxFileAge = 3600;
    
    function _nocache(){
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }
    
    // Make sure the fileName is unique but only if chunking is disabled
    function _check_file_unique($targetDir,$fileName){
        if (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);

            $count = 1;
            while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                $count++;

            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }
        return $fileName;
    }
    //clean temp dir files
    function clean_tmp_dir($targetDir){
        if (is_dir($targetDir) && ($dir = @opendir($targetDir))) {
            while (($file = readdir($dir)) !== false) {
                $filePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                if (filemtime($filePath) < time() - $this->maxFileAge)
                    @unlink($filePath);
            }

            closedir($dir);
            return true;
        } else{
            return false;
        }
    }
    
    /*return 0: success
            100: Failed to open temp directory.
            101: Failed to open input stream.
            102: Failed to open output stream.
            */
    function upload_multi($targetDir,$chunk=0,$chunks=0,$fileName='',$cleanupTargetDir=false){
        @set_time_limit(5 * 60);
        $this->_nocache();

        $fileName = preg_replace('/[^\w\._]+/', '', $fileName);
        if($chunk == 0 || $chunks < 2){
            $fileName = $this->_check_file_unique($targetDir,$fileName);
        }

        if (!file_exists($targetDir))
            @mkdir($targetDir);

        if($cleanupTargetDir){
            if(!$this->clean_tmp_dir($targetDir)){
                return 100;
            }
        }
        
        $out = @fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
        if ($out) {
            $in = @fopen("php://input", "rb");
            if ($in) {
                while ($buff = fread($in, 4096))
                    fwrite($out, $buff);
            } else
                return 101;
            fclose($in);
            fclose($out);
        } else{
            return 102;
        }
        return 0;
    }
}