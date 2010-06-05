<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class output{
    var $data = array();
    
    function set($key,$value){
        $this -> data[$key] = $value;
    }
    
    function get($key = ''){
        if(!$key) return $this -> data;
        else  return isset($this->data[$key]) ? $this->data[$key] : '';
    }
}