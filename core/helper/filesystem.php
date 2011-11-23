<?php

if(!function_exists('file_put_contents')) {
    function file_put_contents($filename, $s) {
        $fp = @fopen($filename, 'w');
        @fwrite($fp, $s);
        @fclose($fp);
        return TRUE;
    }
}
//文件后缀
function file_ext($filename){
    return strtolower(end(explode('.',$filename)));
}
//文件除去后缀的文件名
function file_pure_name($filename){
    $arr = explode('.',$filename);
    array_pop($arr);
    return implode('.',$arr);
}
//删除文件夹
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
//文件夹占用空间
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
//清除文件夹内文件
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

function dir_writeable($dir) {
    $writeable = 0;
    if(!is_dir($dir)) {
        @mkdir($dir, 0777);
    }
    if(is_dir($dir)) {
        if($fp = @fopen("$dir/test.txt", 'w')) {
            @fclose($fp);
            @unlink("$dir/test.txt");
            $writeable = 1;
        } else {
            $writeable = 0;
        }
    }
    return $writeable;
}