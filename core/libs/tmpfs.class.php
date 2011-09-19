<?php
/**
 * $Id$
 * 
 *      
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 - 2011 meiu.cn lingter@gmail.com
 */
class tmpfs_cla{

    function tmpfs_cla(){
        $this->targetDir = ROOTDIR.'cache'.DIRECTORY_SEPARATOR.'tmp';
    }

    function get_path($fileName){
        $fileName = preg_replace('/[^\w\._]+/', '', $fileName);
        return $this->targetDir . DIRECTORY_SEPARATOR . $fileName;
    }

    function write($fileName,$content,$append=false,$fullPath=false){
        if (!file_exists($this->targetDir))
            @mkdir($this->targetDir);
        
        if($fullPath){
             $filePath = $fileName;
        }else{
            $filePath = $this->targetDir . DIRECTORY_SEPARATOR . $fileName;
        }
        if($append){
            return file_put_contents($filePath,$content,FILE_APPEND);
        }
        return file_put_contents($filePath,$content);
    }

    function read($fileName,$fullPath=false){
        if($fullPath){
             $filePath = $fileName;
        }else{
            $filePath = $this->targetDir . DIRECTORY_SEPARATOR . $fileName;
        }
        return file_get_contents($filePath);
    }

    function delete($fileName,$fullPath=false){
        if($fullPath){
             $filePath = $fileName;
        }else{
            $filePath = $this->targetDir . DIRECTORY_SEPARATOR . $fileName;
        }
        return @unlink($filePath);
    }

    function upload($fileName,$append=false,$fullPath=false){ 
        $fileName = preg_replace('/[^\w\._]+/', '', $fileName);

        if (!file_exists($this->targetDir))
            @mkdir($this->targetDir);
            
        if($fullPath){
             $filePath = $fileName;
        }else{
            $filePath = $this->targetDir . DIRECTORY_SEPARATOR . $fileName;
        }

        $out = @fopen($filePath, !$append ? "wb" : "ab");
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