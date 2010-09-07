<?php
/**
 * $Id: storager.php 43 2010-07-07 01:42:43Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class storager{
    
    function storager(){
        $this->class_name = defined('WITH_STORAGER')?constant('WITH_STORAGER'):'fs_storage';
        require_once(LIBDIR.'/storager/'.$this->class_name.'.php');
        $this->worker = new $this->class_name;
    }
    
    function save($file,$path){
        
    }
}